<?php 
// src/Service/AppManager.php

namespace App\Service;

use App\Entity\User;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppManager
{
    private $entityManager;
    private $eventRepository;

    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
    }

    public function registerUser(array $userData): User
    {
        $user = new User();
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, array $userData): User
    {
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        if (isset($userData['password'])) {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function createEvent(array $eventData, User $creator): Event
    {
        $event = new Event();
        $event->setTitle($eventData['title']);
        $event->setDescription($eventData['description']);
        $event->setBeginDate(new \DateTime($eventData['begin_date']));
        $event->setEndDate(new \DateTime($eventData['end_date']));
        $event->setPlace($eventData['place']);
        $event->setCreator($creator);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }

    public function updateEvent(Event $event, array $eventData): Event
    {
        $event->setTitle($eventData['title']);
        $event->setDescription($eventData['description']);
        $event->setBeginDate(new \DateTime($eventData['begin_date']));
        $event->setEndDate(new \DateTime($eventData['end_date']));
        $event->setPlace($eventData['place']);

        $this->entityManager->flush();

        return $event;
    }

    public function deleteEvent(Event $event): void
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }

    public function getEventsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->eventRepository->findByDateRange($startDate, $endDate);
    }
    
    
    

    public function getAllUpcomingEvents(): array
    {
        return $this->eventRepository->findUpcomingEvents();
    }

   
}
