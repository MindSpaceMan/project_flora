<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\CheckoutRequest;
use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CheckoutService
{
    public function __construct(
        private OrderRepository        $orders,
        private EntityManagerInterface $em,
        private MailerInterface        $mailer,
        private string                 $orderNotifyEmail,
        private string                 $mailerFrom,
        private string                 $mailerFromName,
        private ?LoggerInterface       $logger = null,
    )
    {
    }

    /**
     * Выполняет checkout для анонимного пользователя по cart token.
     * Меняет статус new -> sent и отправляет письмо клиенту (async через Messenger).
     * @throws OptimisticLockException
     */

    public function checkout(string $cartToken, CheckoutRequest $dto): Order
    {

        // 1) Найти заказ и повесить блокировку
        $order = $this->orders->findNewByCartToken($cartToken);
        if (!$order) {
            throw new NotFoundHttpException('Корзина не найдена или уже оформлена.');
        }
        // Важно: блокировка требует активной транзакции
        $this->em->lock($order, LockMode::PESSIMISTIC_WRITE);

        // 3) Проверка, что в заказе есть позиции
        if (method_exists($order, 'getItems') && \count($order->getItems()) === 0) {
            throw new BadRequestHttpException('Нельзя оформить пустую корзину.');
        }

        // 4) Создаём/сохраняем клиента
        $customer = new Customer();
        $customer->setName($dto->fullName);
        $customer->setPhone($dto->phone);
        $customer->setEmail($dto->email);
        $customer->setComment($dto->comment);
        $this->em->persist($customer);
        $this->em->flush();

        // 5) Адрес (если нужен)
        $address = new \App\Entity\Address();
        $address->setLine1($dto->deliveryAddress);
        $address->setCity($dto->city);
        $address->setRegion($dto->region);
        $address->setZip($dto->zip);
        $this->em->persist($address);
        $this->em->flush();

        $customer->addAddress($address);
        $customer->addOrder($order);


        $order->setStatus('sent'); // после checkout
        $this->em->flush();


        if ($dto->email) {
            try {
                $email = (new TemplatedEmail())
                    ->from(new Address($this->mailerFrom, $this->mailerFromName))
                    ->to(new Address($dto->email, $dto->fullName ?: 'Клиент'))
                    ->subject(sprintf('Ваш заказ #%s принят', $order->getId()->toString()))
                    ->htmlTemplate('email/order_checkout.html.twig')
                    ->context([
                        'order' => $order,
                        'customer' => $customer,
                        'address' => $address,
                    ]);

                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->logger?->error('Mail to customer failed', ['e' => $e]);
            }
        }

        // Письмо админу/менеджеру
        if (!empty($this->orderNotifyEmail)) {
            $recipients = array_filter(array_map('trim', explode(',', $this->orderNotifyEmail)));
            foreach ($recipients as $recipient) {
                try {
                    $adminEmail = (new TemplatedEmail())
                        ->from(new Address($this->mailerFrom, $this->mailerFromName))
                        ->to(new Address($recipient))
                        ->subject(sprintf('Новый заказ #%s (%s)', $order->getId()->toString(), $order->getStatus()))
                        ->htmlTemplate('email/order_admin_notification.html.twig')
                        ->context([
                            'order' => $order,
                            'customer' => $customer,
                            'address' => $address,
                            'newsletter' => $dto->newsletterOptIn,
                        ]);

                    $this->mailer->send($adminEmail);
                } catch (TransportExceptionInterface $e) {
                    $this->logger?->error('Mail to admin failed', ['recipient' => $recipient, 'e' => $e]);
                }
            }
        } else {
            $this->logger?->warning('ORDER_NOTIFY_EMAIL is empty — admin notification skipped.');
        }
        return $order;
    }
}