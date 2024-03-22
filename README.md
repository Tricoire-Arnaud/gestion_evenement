# Gestion des événements

Ce projet vise à développer une application de gestion d'événements permettant aux utilisateurs de créer, modifier, supprimer et s'inscrire à des événements.

Ce dépôt contient le code pour l'interface administrateur de l'application de gestion des événements.

## Installation de l'interface administrateur

Après avoir cloné le dépôt, suivez les étapes ci-dessous pour installer l'interface administrateur :

1. Assurez-vous d'être sur la branche `main` en utilisant la commande `git checkout main`.

2. Dans votre terminal, exécutez `composer install` pour installer les dépendances PHP nécessaires.

3. Créez un fichier `.env.local` à la racine du projet.

4. Copiez et collez les lignes suivantes dans le fichier `.env.local` :

   ```plaintext
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/event_management?serverVersion=10.4.20-MariaDB&charset=utf8mb4"
   APP_SECRET=your_app_secret_key
   ```

   Assurez-vous de remplacer `username` et `password` par les identifiants de votre base de données et `your_app_secret_key` par une clé secrète unique pour votre application.

5. Dans votre terminal, exécutez les commandes suivantes pour créer la base de données et exécuter les migrations :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

   Si Doctrine demande confirmation pour exécuter les migrations, répondez `yes`.

6. Ensuite, vous pouvez charger des données de démonstration en utilisant la commande :

   ```bash
   php bin/console doctrine:fixtures:load
   ```

   Si Doctrine demande confirmation pour purger la base de données, répondez `yes`.

Une fois ces étapes terminées, l'interface de l'application de gestion des événements devrait être installée et prête à être utilisée.

## Fonctionnalités

1. **Fixtures :** Des fixtures sont disponibles pour générer des comptes utilisateurs ainsi que des événements. Les événements sont caractérisés par un titre, une description, une date et heure de début, une date et heure de fin, ainsi qu'un lieu.

2. **Inscription et Connexion :**
   - Une page d'inscription permet aux utilisateurs de saisir leur nom, leur adresse e-mail et un mot de passe respectant des critères spécifiques (> 8 caractères, au moins une majuscule, un caractère spécial et un chiffre).
   - Une page de connexion est également disponible.
   - Les utilisateurs peuvent se déconnecter à tout moment de l'application.

3. **Page d'Accueil :**
   - Les événements à venir sont affichés sur la page d'accueil de l'application, même pour les utilisateurs non connectés.
   - Les événements sont présentés sous forme d'une grille et triés par date et heure de début dans l'ordre chronologique.
   - Un filtre est disponible pour choisir un intervalle de date et affiner l'affichage des événements.

4. **Fonctionnalités pour les utilisateurs authentifiés :**
   - Création d'événements.
   - Modification et suppression d'événements, uniquement par les créateurs d'événements.
   - Inscription et désinscription à un événement. Les utilisateurs ne peuvent pas être inscrits plusieurs fois au même événement.
   - Option "Mes événements" permettant aux utilisateurs de n'afficher que les événements auxquels ils sont inscrits.

## Accès aux fonctionnalités

- **Inscription :** Les nouveaux utilisateurs peuvent s'inscrire en accédant à la page d'inscription depuis le lien correspondant sur la page de connexion.
- **Connexion :** Les utilisateurs peuvent se connecter en accédant à la page de connexion à partir du lien correspondant sur la page d'accueil.
- **Création d'événements :** Les utilisateurs connectés peuvent créer de nouveaux événements en accédant à la page de création d'événements à partir de la barre de navigation.
- **Modification et Suppression d'événements :** Les utilisateurs connectés peuvent modifier et supprimer leurs propres événements en accédant à la page de détail de l'événement correspondant.
- **Inscription et Désinscription à un événement :** Les utilisateurs connectés peuvent s'inscrire et se désinscrire à un événement en accédant à la page de détail de l'événement correspondant.

## Choix de Conception

Utilisation de Symfony pour le développement de l'application back-end.
Utilisation de Doctrine ORM pour la gestion de la base de données.
Utilisation de JWT pour l'authentification des utilisateurs.
Utilisation de Bootstrap pour le design de l'interface utilisateur.

## Limitations Éventuelles

La sécurité des données sensibles comme les mots de passe pourrait être renforcée avec des techniques supplémentaires telles que la mise en place de JWT Token.
Certaines fonctionnalités avancées comme la pagination peuvent ne pas être implémentées.
L'application ne prend pas encore en charge la gestion des rôles et des autorisations.