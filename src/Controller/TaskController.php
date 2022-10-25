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

    #[Route('/tasks', name: 'app_tasks')]
    public function index(CategoryRepository $categoryRepo): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $categoryRepo->findAll(),
            'categories' => $this->taskRepo->findAll()
        ]);
    }
}
