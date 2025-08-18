<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\CheckoutRequest;
use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CheckoutService
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private MailerInterface $mailer,
        private string $orderNotifyEmail,              // <-- из env
        private string $mailerFrom,         // from env
        private string $mailerFromName,     // from env
        private ?LoggerInterface $logger = null,       // опционально
    ) {}

    /**
     * Выполняет checkout для анонимного пользователя по cart token.
     * Меняет статус new -> sent и отправляет письмо клиенту (async через Messenger).
     */
    public function checkout(string $cartToken, CheckoutRequest $dto): Order
    {
        $order = $this->orders->findNewByCartToken($cartToken);
        if (!$order) {
            throw new NotFoundHttpException('Корзина не найдена или уже оформлена.');
        }

        $errors = $this->validator->validate($dto);
        if (\count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $customer = new Customer();
        // Записываем контактные данные в заказ (поля подгони под твою сущность)
        $customer->setName($dto->fullName);
        $customer->setPhone($dto->phone);
        $customer->setEmail($dto->email);
        $customer->setComment($dto->comment);
        $this->em->persist($customer);
        $this->em->flush();

        $address = new \App\Entity\Address();
        $address->setLine1($dto->deliveryAddress);
        $address->setCity($dto->city);
        $address->setRegion($dto->region);
        $address->setZip($dto->zip);
        $this->em->persist($address);
        $this->em->flush();

        $customer->addAddress($address);
        $customer->addOrder($order);

        $order->setStatus('sent'); // после checkout (on email)

        $this->em->flush();

        // Шлём письмо клиенту (и/или менеджеру). Важно: включена интеграция с Messenger, так что письмо уйдет в очередь.
        if($dto->email) {
            $email = (new TemplatedEmail())
                ->from(new Address($this->mailerFrom, $this->mailerFromName))
                ->to(new Address($dto->email, $dto->fullName ?: 'Клиент'))
                ->subject(sprintf('Ваш заказ №%s принят', $order->getId()))
                ->htmlTemplate('email/order_checkout.html.twig')
                ->context([
                    'order' => $order,
                ]);

            // Отправляем — при включенном messenger mailer отправится асинхронно
            $this->mailer->send($email);
        }

        // ---- Письмо админу (async) ----
        if (!empty($this->orderNotifyEmail)) {
            // Можно поддержать список через запятую в env
            $recipients = array_filter(array_map('trim', explode(',', $this->orderNotifyEmail)));
            foreach ($recipients as $recipient) {
                $adminEmail = (new TemplatedEmail())
                    ->from(new Address($this->mailerFrom, $this->mailerFromName))
                    ->to(new Address($recipient))
                    ->subject(sprintf('Новый заказ №%s (%s)', $order->getId(), $order->getStatus()))
                    ->htmlTemplate('email/order_admin_notification.html.twig')
                    ->context([
                        'order'        => $order,
                        'fullName'     => $dto->fullName,
                        'phone'        => $dto->phone,
                        'email'        => $dto->email,
                        'address'      => $dto->deliveryAddress,
                        'comment'      => $dto->comment,
                        'newsletter'   => $dto->newsletterOptIn,
                    ]);

                $this->mailer->send($adminEmail);
            }
        } else {
            $this->logger?->warning('ORDER_NOTIFY_EMAIL is empty — admin notification skipped.');
        }


        return $order;
    }
}