<?php

namespace App\Service;

use App\Entity\Task;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(private MailerInterface $mailer){}

    public function sendEmailToAdminWhenIsDone(Task $task) {
        $email = (new Email())
            ->from('admin@taskplanner.fr')
            ->to('camile@camile.fr')
            ->subject('change status')
            ->text('supprime si tu veux :')
            ->html('<a href="https://localhost:8000/task/' . $task->getId() . '">See Twig integration for better HTML integration!</a>');

        $this->mailer->send($email);
    }
}