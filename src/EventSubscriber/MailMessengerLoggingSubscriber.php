<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

final readonly class MailMessengerLoggingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.mail')]
        private LoggerInterface $mailLogger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onHandled',
            WorkerMessageFailedEvent::class  => 'onFailed',
        ];
    }

    public function onHandled(WorkerMessageHandledEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $message  = $envelope->getMessage();

        // логируем только письма
        if (!$message instanceof SendEmailMessage) {
            return;
        }

        $id = $envelope->last(TransportMessageIdStamp::class)?->getId();
        $this->mailLogger->info('Email message handled successfully', [
            'message_class' => $message::class,
            'queue_id'      => $id,
            'transport'     => 'async',
        ]);
    }

    public function onFailed(WorkerMessageFailedEvent $event): void
    {
        $envelope  = $event->getEnvelope();
        $message   = $envelope->getMessage();

        if (!$message instanceof SendEmailMessage) {
            return;
        }

        $e   = $event->getThrowable();
        $id  = $envelope->last(TransportMessageIdStamp::class)?->getId();

        // Если willRetry() === true — это промежуточный фэйл (будет ретрай).
        // Если false — окончательная неудача (улетит в failure transport).
        $level = $event->willRetry() ? 'warning' : 'error';

        $this->mailLogger->log($level, 'Email message failed', [
            'message_class' => $message::class,
            'queue_id'      => $id,
            'transport'     => 'async',
            'will_retry'    => $event->willRetry(),
            'error'         => $e->getMessage(),
            'trace'         => $e->getTraceAsString(),
        ]);
    }
}