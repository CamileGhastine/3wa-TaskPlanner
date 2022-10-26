<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(private TaskRepository $taskRepo) {
    }

    #[Route('/task', name: 'app_tasks')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->taskRepo->findAllTaskWithUserAndCategory(),
            'categories' => $categoryRepo->findAll(),
        ]);
    }

    #[Route('/task/{id<[0-9]+>}', name: 'app_show_task')]
    public function show(int $id): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $this->taskRepo->find($id),
        ]);
    }
}
