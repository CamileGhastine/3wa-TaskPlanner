<?php

use \PHPUnit\Framework\TestCase;
use App\Service\UrgentCalculator;
use App\Repository\TaskRepository;
use App\Entity\Task;

class UrgentCalculatorTest extends TestCase
{

    private UrgentCalculator $urgentCalculator;
    private Task $task;

    public function setUp(): void
    {
        $this->task = new Task;
        $this->task->setTitle('title')
            ->setUrgent(['color' => 'success','label' => 'To Do']);

        $this->urgentCalculator =  new UrgentCalculator($this->createMock(TaskRepository::class));
    }

    public function testUrgentCalculatorWhenEntryIsObject()
    {
        $this->urgentCalculator->makeAllTaskUrgent($this->task);

        $this->assertSame($this->task->getUrgent() , ['color' => 'danger','label' => 'URGENT']);

    }

    public function testUrgentCalculatorWhenEntryIsArray()
    {
        $task = new Task;
        $task->setUrgent(['color' => 'warning','label' => 'Rappel']);

        $tasksArray =[
            new Task,
            $task,
            $this->task
        ];


        $this->urgentCalculator->makeAllTaskUrgent($tasksArray);

        foreach ($tasksArray as $task) {
            $this->assertSame($task->getUrgent() , ['color' => 'danger','label' => 'URGENT']);
        }
    }

    public function testSetUrgencyWhenEntryIsObjectAndDateIsTodayOrLess()
    {
        $task = $this->task->setExpiratedAt(new DateTime());
        $this->urgentCalculator->setUrgency($task);
        $this->assertSame($task->getUrgent(), ['color' => 'danger','label' => 'URGENT'], 'la date du jour est mal traité');

        $task = $this->task->setExpiratedAt((new DateTime())->sub(new DateInterval('P1D')));
        $this->urgentCalculator->setUrgency($task);
        $this->assertSame($task->getUrgent(), ['color' => 'danger','label' => 'URGENT'], 'la date d\'hier est mal traité');
    }

    public function testSetUrgencyWhenEntryIsObjectAndDateIsMoreThanOneWeek()
    {
        $task = $this->task->setExpiratedAt((new DateTime())->add(new DateInterval('P8D')));
        $this->urgentCalculator->setUrgency($task);
        $this->assertSame($task->getUrgent(), ['color' => 'success','label' => 'TO DO'], 'la date dans 1 semaine + 1 jour est mal traité');

        $task = $this->task->setExpiratedAt((new DateTime())->add(new DateInterval('P7D')));
        $this->urgentCalculator->setUrgency($task);
        $this->assertNotSame($task->getUrgent(), ['color' => 'success','label' => 'TO DO'], 'la date dans 1 semaine est mal traité');
    }


    public function testSetUrgencyWhenEntryIsObjectAndDateIsLessThanOneWeek()
    {
        $task = $this->task->setExpiratedAt(new DateTime());
        $this->urgentCalculator->setUrgency($task);
        $this->assertNotSame($task->getUrgent(), ['color' => 'warning', 'label' => 'Rappel'], 'la date du jour est mal traité');

        $task = $this->task->setExpiratedAt((new DateTime())->add(new DateInterval('P2D')));
        $this->urgentCalculator->setUrgency($task);
        $this->assertSame($task->getUrgent(), ['color' => 'warning', 'label' => 'Rappel'], 'la date du lendemain est mal traité');
    }

}




