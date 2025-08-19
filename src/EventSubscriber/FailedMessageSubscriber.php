<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FailedMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        if ($event->willRetry()) {
            return; // Not a final failure yet (it will be retried)
        }
        $message = $event->getEnvelope()->getMessage();
        $exception = $event->getThrowable();

        $this->logger->error('Messenger message permanently failed', [
            'message_class' => $message::class,
            'message_body'  => method_exists($message, '__toString')
                ? (string) $message
                : json_encode(get_object_vars($message), JSON_UNESCAPED_UNICODE),
            'exception'     => $exception->getMessage(),
            'trace'         => $exception->getTraceAsString(),
        ]);
    }
}
