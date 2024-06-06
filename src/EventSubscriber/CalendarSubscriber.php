<?php

namespace App\EventSubscriber;

use App\Repository\EventRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\SetDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly UrlGeneratorInterface $router
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(SetDataEvent $setDataEvent): void
    {
        $start = $setDataEvent->getStart();
        $end = $setDataEvent->getEnd();
        $filters = $setDataEvent->getFilters();

        $events = $this->eventRepository
            ->createQueryBuilder('event')
            ->where('event.dateBegin BETWEEN :start and :end OR event.$ateEnd BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($events as $event) {
            // this create the events with your data (here booking data) to fill calendar
            $bookingEvent = new Event(
                $event->getTitle(),
                $event->getBeginAt(),
                $event->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             */
            $bookingEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);
            $bookingEvent->addOption(
                'url',
                $this->router->generate('app_admin_event_show', [
                    'id' => $event->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $setDataEvent->addEvent($bookingEvent);
        }
    }
}