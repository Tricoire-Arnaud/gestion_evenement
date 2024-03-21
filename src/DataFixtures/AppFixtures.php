<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = [];

        // Charger les utilisateurs    
        $user = new User();
        $user->setName($faker->name);
        $user->setEmail('toto@toto.com');
        $user->setPassword('$2y$13$eG0kayQuj/Ws0lgHbni5fOs58zUs01ZrIaLkFcJL7BFMxEclitv4m ');
        $manager->persist($user);
        $users[] = $user;

        $user = new User();
        $user->setName($faker->name);
        $user->setEmail('tata@tata.com');
        $user->setPassword('$2y$13$XVRpqcrdH3apeyhEkY4f5ONHgwh1pEGd.0eH8WOsc2OjI4R3VgLA6');
        $manager->persist($user);
        $users[] = $user;

        $user = new User();
        $user->setName($faker->name);
        $user->setEmail('titi@titi.com');
        $user->setPassword('$2y$13$bvCZAYRLl1gebkB9n780sewxyXY3XZJPpKJLefXyj67HkXD8GHvRW ');
        $manager->persist($user);
        $users[] = $user;

        $manager->flush();

        // Génération de quelques événements associés à chaque utilisateur
        foreach ($users as $user) {
            for ($j = 0; $j < 20; $j++) {
                $event = new Event();
                $event->setTitle($faker->sentence);
                $event->setDescription($faker->paragraph);

                // Génération de la date de début de l'événement
                $startDate = $faker->dateTimeBetween('now', '+1 week');
                // Génération de la date de fin de l'événement en ajoutant un nombre aléatoire de jours à la date de début
                $endDate = clone $startDate;
                $endDate->modify('+' . $faker->numberBetween(1, 30) . ' days');

                // Vérifier si la date de fin est antérieure à la date de début et ajuster si nécessaire
                if ($endDate <= $startDate) {
                    $endDate = clone $startDate;
                    $endDate->modify('+1 day');
                }

                $event->setBeginDate($startDate);
                $event->setEndDate($endDate);
                $event->setPlace($faker->address);
                // Définition de l'utilisateur créateur de l'événement
                $event->setCreator($user);
                $manager->persist($event);
            }
        }

        // Persistez les données dans la base de données
        $manager->flush();
    }
}
