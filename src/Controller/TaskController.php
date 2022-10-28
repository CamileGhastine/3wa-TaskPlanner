<?php

namespace App\Controller;

use App\Entity\Task;
use App\Event\EventIsDoneChange;
use App\Form\TaskType;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Service\UrgentCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepo,
        private CategoryRepository $categoryRepo,
        private UrgentCalculator $urgentCalculator,
        private MailerInterface $mailer
    ) {
    }

    #[Route('/task', name: 'app_tasks')]
    public function index(): Response
    {
        $tasks = $this->taskRepo->findAllTaskWithUserAndCategory();

        $tasks = $this->urgentCalculator->setUrgency($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'categories' => $this->categoryRepo->findAll(),
            ]);
    }

    #[Route('/task/category/{id<[0-9]+>}', name: 'app_tasks_by_category')]
    public function indexByCategory(int $id): Response
    {
        $tasks = $this->taskRepo->findAllTaskWithUserAndCategoryByCategory($id);

        $tasks = $this->urgentCalculator->setUrgency($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'categories' => $this->categoryRepo->findAll(),
        ]);
    }

    #[Route('/task/{id<[0-9]+>}', name: 'app_show_task')]
    #[Entity('task', expr: 'repository.findTaskByIdWithUserCategoryAndTag(id)')]
    public function show(Task $task): Response
    {
        $task = $this->urgentCalculator->setUrgency($task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/task/search', name: 'app_tasks_search')]
    public function search(Request $request): Response
    {
        $search = $request->request->get('search');

        return $this->render('task/index.html.twig', [
            'tasks' => $this->taskRepo->findByKeyWOrd($search),
            'categories' => $this->categoryRepo->findAll(),
        ]);
    }

    #[Route('/task/delete/{id<[0-9]+>}', name: 'app_task_delete')]
    public function delete(Task $task, TaskRepository $taskRepo, Request $request): Response
    {
        if(
            !$this->isGranted('ROLE_ADMIN')
            || !$task->isIsDone()
            || !$this->isCsrfTokenValid('delete_task'. $task->getId() , $request->query->get('token_csrf'))
        )
        {
            $this->addFlash('error', 'Interdit de supprimer' );

            return $this->redirectToRoute('app_show_task', ['id' => $task->getId()]);
        }

        $taskRepo->remove($task, true);

        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/task/status/{id<[0-9]+>}', name: 'app_task_done')]
    public function changeIsDone(Task $task, Request $request, EventDispatcherInterface $dispatcher): Response
    {
        if(!$this->isCsrfTokenValid('change_done'. $task->getId(), $request->query->get('token_csrf'))){
        $this->addFlash('error', 'Interdit de changer le status de la tâche' );

        return $this->redirectToRoute('app_show_task', ['id' => $task->getId()]);
        }

       $task->setIsDone(!$task->isIsDone());

       $this->taskRepo->save($task, true);

       $event = New EventIsDoneChange($this->mailer, $task);
       $dispatcher->dispatch($event, 'EventIsDoneChange');

       return $this->redirectToRoute('app_show_task', ['id' => $task->getId()]);
    }

    #[Route('/task/create', name:'app_task_create', methods:['POST', 'GET'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if(!$this->getUser()) {
            $this->addFlash('error', 'merci de vous connecter pour créer une tâche');

            return $this->redirectToRoute('app_login');
        }

        $task = new Task();
        $task->setIsDone(false)
            ->setUser($this->getUser())
        ;

        $taskForm = $this->createForm(TaskType::class, $task);

        $taskForm->handleRequest($request);

        if($taskForm->isSubmitted()) {
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('app_show_task', ['id' => $task->getId()]);
        }

        return $this->render('/task/create.html.twig', [
            'taskForm' => $taskForm->createView(),

        ]);
    }
}
