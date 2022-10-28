<?php

namespace App\EventSubscriber;

use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventIsDoneChangeSubscriber implements EventSubscriberInterface
{
    public function __construct(private Mailer $mailer) {}


    public function onEventIsDoneChange($event): void
    {
       $this->mailer->sendEmailToAdminWhenIsDone($event->getTask());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'EventIsDoneChange' => 'onEventIsDoneChange',
        ];
    }
}
