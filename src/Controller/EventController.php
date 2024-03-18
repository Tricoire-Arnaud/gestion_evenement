<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\AppManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    private AppManager $appManager;

    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }

    #[Route('/events/create', name: 'event_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->appManager->createEvent($form->getData(), $this->getUser());

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
            return $this->redirectToRoute('event_list');
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
            return $this->redirectToRoute('event_list');
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
        return $this->redirectToRoute('event_list');
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
        $events = $this->appManager->getAllEvents();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }
}
