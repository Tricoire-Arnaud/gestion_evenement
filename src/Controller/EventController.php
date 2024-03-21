<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\AppManager;
use App\Form\DateRangeFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        // Créer le formulaire de filtrage par plage de dates
        $form = $this->createForm(DateRangeFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $start_date = \DateTime::createFromFormat('Y-m-d', $data['start_date']);
            $end_date = \DateTime::createFromFormat('Y-m-d', $data['end_date']);

            if (!$start_date || !$end_date) {
                throw new \InvalidArgumentException('Format de date invalide.');
            }

            // Récupérer les événements dans la plage de dates spécifiée
            $events = $appManager->getEventsByDateRange($start_date, $end_date);
        } else {
            // Récupérer tous les événements à venir
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
        // Créer le formulaire de création d'événement
        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventData = [
                'title' => $form->get('title')->getData(),
                'description' => $form->get('description')->getData(),
                'begin_date' => $form->get('beginDate')->getData()->format('Y-m-d H:i:s'),
                'end_date' => $form->get('endDate')->getData()->format('Y-m-d H:i:s'),
                'place' => $form->get('place')->getData(),
            ];

            $creator = $this->getUser();

            // Créer l'événement avec les données fournies
            $this->appManager->createEvent($eventData, $creator);

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/my-events', name: 'user_events')]
    public function userEvents(AppManager $appManager): Response
    {
        $user = $this->getUser();
        // Récupérer les événements créés par l'utilisateur
        $createdEvents = $appManager->getEventsByUser($user);
        // Récupérer les événements auxquels l'utilisateur est inscrit
        $registeredEvents = $appManager->getRegisteredEventsByUser($user);

        return $this->render('event/user_events.html.twig', [
            'createdEvents' => $createdEvents,
            'registeredEvents' => $registeredEvents,
        ]);
    }


    #[Route('/events/{id}/edit', name: 'event_edit')]
    public function edit(Event $event, Request $request): Response
    {
        // Créer le formulaire de modification d'événement avec les données de l'événement existant
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventData = [
                'title' => $form->get('title')->getData(),
                'description' => $form->get('description')->getData(),
                'begin_date' => $form->get('beginDate')->getData()->format('Y-m-d H:i:s'),
                'end_date' => $form->get('endDate')->getData()->format('Y-m-d H:i:s'),
                'place' => $form->get('place')->getData(),
            ];

            // Mettre à jour l'événement avec les nouvelles données
            $this->appManager->updateEvent($event, $eventData);

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');
            return $this->redirectToRoute('home');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}/register', name: 'event_register')]
    public function register(Event $event): RedirectResponse|Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($event->getRegisteredUsers()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('home');
        }

        // Ajouter l'utilisateur à la liste des inscrits à l'événement
        $event->addRegisteredUser($user);
        $this->appManager->saveEvent($event);

        $this->addFlash('success', 'Inscription réussie à l\'événement.');
        return $this->redirectToRoute('home');
    }

    #[Route('/event/{id}/unregister', name: 'event_unregister', methods: ['POST'])]
    public function unregisterEvent(Event $event, AppManager $appManager): Response
    {
        $user = $this->getUser();

        // Désinscrire l'utilisateur de l'événement
        $appManager->unregisterEvent($event, $user);

        return $this->redirectToRoute('home');
    }

    #[Route('/events/{id}/delete', name: 'event_delete')]
    public function delete(Event $event): Response
    {
        // Supprimer l'événement
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
