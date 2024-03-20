<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\AppManager;
use App\Form\DateRangeFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    private AppManager $appManager;

    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }

    #[Route('/', name: 'home')]
    public function home(Request $request, AppManager $appManager): Response
    {
        $form = $this->createForm(DateRangeFilterType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $start_date = \DateTime::createFromFormat('Y-m-d', $data['start_date']);
            $end_date = \DateTime::createFromFormat('Y-m-d', $data['end_date']);
    
            // Vérifiez si les dates ont été correctement parsées
            if (!$start_date || !$end_date) {
                throw new \InvalidArgumentException('Format de date invalide.');
            }
    
            $events = $appManager->getEventsByDateRange($start_date, $end_date);
        } else {
            $events = $appManager->getAllUpcomingEvents();
        }
    
        return $this->render('event/home.html.twig', [
            'form' => $form->createView(),
            'events' => $events,
        ]);
    }

    #[Route('/events/create', name: 'event_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->appManager->createEvent($form->getData(), $this->getUser());

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}/edit', name: 'event_edit')]
    public function edit(Event $event, Request $request): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->appManager->updateEvent($event, $form->getData());

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}/delete', name: 'event_delete')]
    public function delete(Event $event): Response
    {
        $this->appManager->deleteEvent($event);

        $this->addFlash('success', 'L\'événement a été supprimé avec succès.');
        return $this->redirectToRoute('home');

    }

    #[Route('/events/{id}', name: 'event_show')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
    
    #[Route('/events', name: 'event_list')]
    public function list(): Response
    {
        return $this->redirectToRoute('home');
    }
}
