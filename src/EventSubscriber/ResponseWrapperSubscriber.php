<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseWrapperSubscriber implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $event->setResponse(new JsonResponse(
            [
                'error' => $throwable->getMessage()
            ],
            400
        ));
    }

    public function onResponseEvent(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response instanceof JsonResponse) {
            $content = json_decode($response->getContent(), true);

            if (null === $content || isset($content['error']) || isset($content['data'])) {
                return;
            }

            $event->setResponse(
                new JsonResponse(
                    [
                        'data' => $content,
                    ],
                    $response->getStatusCode(),
                    $response->headers->all()
                )
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
