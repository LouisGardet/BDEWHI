<?php

namespace App\EventSubscriber;

use App\Repository\EventRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\SetDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $eventRepository;
    private $router;

    public function __construct(EventRepository $eventRepository, UrlGeneratorInterface $router)
    {
        $this->eventRepository = $eventRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            SetDataEvent::class => 'onSetData',
        ];
    }

    public function onSetData(SetDataEvent $event)
    {
        $events = $this->eventRepository->findAll();

        foreach ($events as $calendarEvent) {
            $event->addEvent(new Event(
                $calendarEvent->getTitle(),
                $calendarEvent->getStartDate(),
                $calendarEvent->getEndDate() 
            ));
        }
    }
}