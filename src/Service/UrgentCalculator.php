<?php
namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateInterval;
use DateTime;

class UrgentCalculator
{
    public function __construct(private TaskRepository $taskRepo) {}

    public function calcul(): array
    {
        $tasks = $this->taskRepo->findAllTaskWithUserAndCategory();

        foreach ($tasks as $task) {
            /** @var Task $task */
            $date = $task->getExpiratedAt();
            $now = (new DateTime());
            $oneWeek = new DateInterval('P7D');
            $oneWeekAgo = $now->sub($oneWeek);

            if ($date >= $now) {
                $urgent = [
                    'color' => 'danger',
                    'label' => 'URGENT'
                ];
            } else if ($date < $oneWeekAgo) {
                $urgent = [
                    'color' => 'warning',
                    'label' => 'Rappel'
                ];
            } else {
                $urgent = [
                    'color' => 'succes',
                    'label' => 'To Do'
                ];
            }
            $task->setUrgent($urgent);
        }

        return $tasks;
    }

}