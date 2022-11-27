<?php
namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateInterval;
use DateTime;

// Permet de gérer la pastille Urgence d'une tâche envoyée à la vue
class UrgentCalculator
{
    public function __construct(private TaskRepository $taskRepo) {}

    public function setUrgency($tasks): array|Task
    {
        $returnEntity = false;

        if (!is_array($tasks)) {
            $tasks = [$tasks];
            $returnEntity = true;
        }

        foreach ($tasks as $task) {
            /** @var Task $task */
            $date = $task->getExpiratedAt();
            $now = (new DateTime());
            $oneWeek = new DateInterval('P7D');
            $oneWeekAgo = (new DateTime())->add($oneWeek);

            if ($date <= $now) {
                $urgent = [
                    'color' => 'danger',
                    'label' => 'URGENT'
                ];
            } else if ($date > $oneWeekAgo) {
                $urgent = [
                    'color' => 'success',
                    'label' => 'TO DO'
                ];
            } else {
                $urgent = [
                    'color' => 'warning',
                    'label' => 'Rappel'
                ];
            }
            $task->setUrgent($urgent);
        }

        if ($returnEntity == true) {
            return $tasks[0];
        }

        return $tasks;
    }

    public function makeAllTaskUrgent($tasks)
    {
        $returnEntity = false;

        if (!is_array($tasks)) {
            $tasks = [$tasks];
            $returnEntity = true;
        }

        foreach ($tasks as $task) {
            /** @var Task $task */

            $urgent = [
                'color' => 'danger',
                'label' => 'URGENT'
            ];

            $task->setUrgent($urgent);
        }

        if ($returnEntity == true) {
            return $tasks[0];
        }

        return $tasks;
    }

}
