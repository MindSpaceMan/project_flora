<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\MailDto;
use App\Entity\ContactMail;
use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\ContactMailRepository;
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

final readonly class ContactMailService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactMailRepository  $contactMailRepository,
        private MailerInterface        $mailer,
        private string                 $orderNotifyEmail,
        private string                 $mailerFromName,
        private ?LoggerInterface       $logger = null,
    )
    {
    }

    public function contact(MailDto $contactMailDto): string
    {

        $contactMail = new ContactMail();
        $contactMail->setContact($contactMailDto->contact);
        $contactMail->setName($contactMailDto->name);
        $contactMail->setMessage($contactMailDto->message);
        $this->em->persist($contactMail);
        $this->em->flush();
        try {
            $adminEmail = (new TemplatedEmail())
                ->from(new Address($contactMail->getContact(), $contactMail->getName()))
                ->to(new Address($this->orderNotifyEmail, $this->mailerFromName))
                ->subject('Новое сообщение от пользователя')
                ->htmlTemplate('email/contact_mail.html.twig')
                ->context([
                    'mail' => $contactMail,
                ]);

            $this->mailer->send($adminEmail);
        } catch (TransportExceptionInterface $e) {
            $this->logger?->error('ContactMailService to admin failed', ['recipient' => $this->orderNotifyEmail, 'e' => $e]);
        }
        return 'ok';
    }

    public function get(): array
    {
        return $this->contactMailRepository->findAll();
    }
}