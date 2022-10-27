<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventIsDoneChangeSubscriber implements EventSubscriberInterface
{
    public function onEventIsDoneChange($event): void
    {
        $event->sendEmail();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'EventIsDoneChange' => 'onEventIsDoneChange',
        ];
    }
}
