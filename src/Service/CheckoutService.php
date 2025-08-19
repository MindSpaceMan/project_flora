<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\CheckoutRequest;
use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Security\CartTokenResolver;
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

final readonly class CheckoutService
{
    public function __construct(
        private CartService            $cartService,
        private CustomerRepository     $customerRepository,
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
     */
    public function checkout(string $token, CheckoutRequest $dto): Order
    {
        $cart = $this->cartService->getCartByToken($token);
        if ($cart->getItems()->count() <= 0) {
            throw new BadRequestHttpException('Нельзя оформить пустую корзину.');
        }

        $customer = $this->customerRepository->getCustomerByEmailOrPhone($dto->email, $dto->phone);
        if (!$customer) {
            $customer = new Customer();
            $customer->setName($dto->fullName);
            $customer->setPhone($dto->phone);
            $customer->setEmail($dto->email);
            $customer->setComment($dto->comment);
            $this->em->persist($customer);
            $this->em->flush();
        }
        $address = new \App\Entity\Address();
        $address->setLine1($dto->deliveryAddress);
        $address->setCity($dto->city);
        $address->setRegion($dto->region);
        $address->setZip($dto->zip);
        $this->em->persist($address);
        $this->em->flush();

        $customer->addAddress($address);
        $customer->addOrder($cart);

        $cart->setStatus('sent');
        $this->em->flush();


        if ($dto->email) {
            try {
                $email = (new TemplatedEmail())
                    ->from(new Address($this->mailerFrom, $this->mailerFromName))
                    ->to(new Address($dto->email, $dto->fullName ?: 'Клиент'))
                    ->subject(sprintf('Ваш заказ #%s принят', $cart->getId()->toString()))
                    ->htmlTemplate('email/order_checkout.html.twig')
                    ->context([
                        'order' => $cart,
                        'customer' => $customer,
                        'address' => $address,
                    ]);

                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->logger?->error('ContactMailService to customer failed', ['e' => $e]);
            }
        }

        if (!empty($this->orderNotifyEmail)) {
            $recipients = array_filter(array_map('trim', explode(',', $this->orderNotifyEmail)));
            foreach ($recipients as $recipient) {
                try {
                    $adminEmail = (new TemplatedEmail())
                        ->from(new Address($this->mailerFrom, $this->mailerFromName))
                        ->to(new Address($recipient))
                        ->subject(sprintf('Новый заказ #%s (%s)', $cart->getId()->toString(), $cart->getStatus()))
                        ->htmlTemplate('email/order_admin_notification.html.twig')
                        ->context([
                            'order' => $cart,
                            'customer' => $customer,
                            'address' => $address,
                        ]);

                    $this->mailer->send($adminEmail);
                } catch (TransportExceptionInterface $e) {
                    $this->logger?->error('ContactMailService to admin failed', ['recipient' => $recipient, 'e' => $e]);
                }
            }
        } else {
            $this->logger?->warning('ORDER_NOTIFY_EMAIL is empty — admin notification skipped.');
        }
        return $cart;
    }
}