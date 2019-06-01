<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonPostSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::CONTROLLER => 'convertJsonStringToArray',
        );
    }

    /**
     * Enable json-body request to be interpreted as request parameters
     * @param ControllerEvent $event
     */
    public function convertJsonStringToArray(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getContentType() !== 'json' || !$request->getContent()) {
            return;
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        $request->request->replace(is_array($data) ? $data : array());
    }
}
