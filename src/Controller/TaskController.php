<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Service\UrgentCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TaskController extends AbstractController
{
    public function __construct(private TaskRepository $taskRepo, private CategoryRepository $categoryRepo) {
    }

    #[Route('/task', name: 'app_tasks')]
    public function index(UrgentCalculator $urgentCalculator): Response
    {
        $tasks = $this->taskRepo->findAllTaskWithUserAndCategory();

        $tasks = $urgentCalculator->setUrgency($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'categories' => $this->categoryRepo->findAll(),
            ]);
    }

    #[Route('/task/category/{id<[0-9]+>}', name: 'app_tasks_by_category')]
    public function indexByCategory(int $id): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->taskRepo->findAllTaskWithUserAndCategoryByCategory($id),
            'categories' => $this->categoryRepo->findAll(),
        ]);
    }

    #[Route('/task/{id<[0-9]+>}', name: 'app_show_task')]
    public function show(int $id, UrgentCalculator $urgentCalculator): Response
    {
        $task = $this->taskRepo->findTaskByIdWithUserCategoryAndTag($id);

        $task = $urgentCalculator->calcul($task);

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
    public function delete(int $id, TaskRepository $taskRepo, Request $request): Response
    {
        $task = $taskRepo->find($id);

        if(
            !$this->isGranted('ROLE_ADMIN')
            || !$task->isIsDone()
            || !$this->isCsrfTokenValid('delete_task'. $task->getId() , $request->query->get('token_csrf'))
        )
        {
            $this->addFlash('error', 'Interdit de supprimer' );

            return $this->redirectToRoute('app_show_task', ['id' => $id]);
        }

        $taskRepo->remove($task, true);

        return $this->redirectToRoute('app_tasks');
    }
}
