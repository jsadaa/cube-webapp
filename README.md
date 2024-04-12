# WEB APP - NEGOSUD

## Description

Cette application est un site de vente en ligne des produits de la société NEGOSUD. 
Elle permet aux clients de consulter les produits disponibles, de les ajouter au panier, de passer des commandes, de consulter l'historique des commandes et des factures.

Elle consomme une API REST pour la gestion des données (cf. [API NEGOSUD](https://github.com/LCE-CESI/ApiCube)).

## Installation

### Prérequis

- PHP 8.2
- Symfony 7.0
- Symfony CLI
- Composer

### Installation du projet

1. Cloner le projet

```bash
git clone git@github.com:LCE-CESI/webapp-symfony.git
```

2. Installer les dépendances

```bash
composer install
```

3. Configurer les variables d'environnement

Créer un fichier `.env.local` à la racine du projet et ajouter les variables d'environnement suivantes :

```env
API_BASE_URL=http://localhost:5273/api
```

4. Démarrer le serveur

```bash
symfony server:start
```

## Utilisation

Ouvrir un navigateur et accéder à l'URL suivante :

```
http://localhost:8000
```

## Auteurs

Léo Paillard