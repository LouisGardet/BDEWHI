<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/event/new', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/fc-load-events', name: 'fc_load_events', methods: ['POST'])]
    public function loadEvents(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();
        $eventsArray = [];

        foreach ($events as $event) {
            $eventsArray[] = [
                'id' => $event->getId(),
                'title' => $event->getName(),
                'start' => $event->getDateBegin()->format('Y-m-d'),
                'end' => $event->getDateEnd()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($eventsArray);
    }
}