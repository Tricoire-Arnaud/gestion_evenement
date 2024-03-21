<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppManager
{
    private $entityManager;
    private $eventRepository;
    private $passwordHasher;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    // Enregistre un utilisateur dans la base de données
    public function registerUser(User $user): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    // Met à jour les informations d'un utilisateur
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

    // Supprime un utilisateur de la base de données
    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    // Crée un nouvel événement
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

    // Met à jour les informations d'un événement
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

    // Supprime un événement de la base de données
    public function deleteEvent(Event $event): void
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();
    }

    // Récupère tous les événements
    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }

    // Enregistre un événement dans la base de données
    public function saveEvent(Event $event): void
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    // Désinscrit un utilisateur d'un événement
    public function unregisterEvent(Event $event, User $user): void
    {
        if (!$event->getRegisteredUsers()->contains($user)) {
            throw new \InvalidArgumentException('L\'utilisateur n\'est pas inscrit à cet événement.');
        }

        $event->removeRegisteredUser($user);

        $this->entityManager->flush();
    }

    // Récupère tous les événements auxquels un utilisateur est inscrit
    public function getRegisteredEventsByUser(User $user): array
    {
        return $user->getRegisteredUsers()->toArray();
    }

    // Récupère tous les événements créés par un utilisateur
    public function getEventsByUser(User $user): array
    {
        $events = $this->entityManager->getRepository(Event::class)->findBy(['creator' => $user]);

        return $events;
    }

    // Récupère tous les événements dans une plage de dates donnée
    public function getEventsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->eventRepository->findByDateRange($startDate, $endDate);
    }

    // Récupère tous les événements à venir
    public function getAllUpcomingEvents(): array
    {
        return $this->eventRepository->findUpcomingEvents();
    }
}
