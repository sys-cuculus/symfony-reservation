# Symfony Reservation

Application de démonstration développée avec Symfony pour présenter mes compétences dans le cadre d'une recherche d'emploi.

Le projet met en scène une plateforme simple de réservation de restaurants, avec une logique autour des propriétaires, des restaurants et des réservations. L'objectif est de montrer une base applicative lisible, testable et proche d'un cas métier concret.

## Objectifs du projet

- Montrer une maîtrise pratique de Symfony et de l'écosystème PHP moderne.
- Structurer une application web autour de contrôleurs, entités, fixtures et tests fonctionnels.
- Présenter un exemple de code compréhensible pour un recruteur ou une équipe technique.
- Servir de support de discussion lors d'un entretien technique en France.

## Fonctionnalités principales

- Gestion des propriétaires.
- Gestion des restaurants.
- Création et consultation de réservations.
- Données de démonstration via fixtures.
- Tests fonctionnels pour les parcours principaux.

## Stack technique

- PHP
- Symfony
- Doctrine ORM
- Twig
- PHPUnit
- Fixtures Symfony/Doctrine

## Installation

Cloner le projet, puis installer les dépendances PHP:

```bash
composer install
```

Configurer les variables d'environnement dans `.env.local`, notamment la connexion à la base de données:

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Créer la base de données et appliquer les migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Charger les données de démonstration:

```bash
php bin/console doctrine:fixtures:load
```

Lancer le serveur de développement:

```bash
symfony server:start
```

ou, sans Symfony CLI:

```bash
php -S 127.0.0.1:8000 -t public
```

L'application est ensuite disponible à l'adresse:

```text
http://127.0.0.1:8000
```

## Tests

Lancer la suite de tests:

```bash
php bin/phpunit
```

Les tests couvrent notamment les contrôleurs liés aux propriétaires, restaurants et réservations.

## Structure du projet

```text
src/Controller/        Contrôleurs Symfony
src/DataFixtures/      Données de démonstration
tests/Controller/      Tests fonctionnels des parcours web
templates/             Vues Twig
config/                Configuration Symfony
public/                Point d'entrée public
```

## Pourquoi ce projet

Ce dépôt a été conçu comme un projet de démonstration pour ma recherche d'emploi en France. Il vise à montrer ma capacité à construire une application Symfony propre, à organiser une logique métier simple, à écrire des tests et à documenter un projet de façon exploitable par une équipe.

## Auteur

Kazuhiro NOMURA
