<?php

namespace App\Controller\admin;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Fetch all users and events from the database
        $users = $userRepository->findAll();
        $events = $eventRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'events' => $events,
        ]);
    }
}