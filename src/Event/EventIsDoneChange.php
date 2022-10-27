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

    public function sendEmail() {
        $email = (new Email())
            ->from('admin@taskplanner.fr')
            ->to('camile@camile.fr')
            ->subject('change status')
            ->text('supprime si tu veux :')
            ->html('<a href="https://localhost:8000/task/' . $this->task->getId() . '">See Twig integration for better HTML integration!</a>');

        $this->mailer->send($email);
    }
}