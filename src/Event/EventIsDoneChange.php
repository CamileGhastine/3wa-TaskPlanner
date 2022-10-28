<?php

namespace App\Event;

use App\Entity\Task;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

class EventIsDoneChange extends Event
{
    private MailerInterface $mailer;
    private Task $task;

    public function __construct($mailer, $task) {
        $this->mailer = $mailer;
        $this->task = $task;
    }

    public function getTask() {
        return $this->task;
    }
}