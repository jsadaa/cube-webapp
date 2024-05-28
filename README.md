# Cube - Webapp : Wine shop

## Description

This app is an online sales site for a fictive company during a school project.
It allows customers to view available products, add them to the cart, place orders, view order history and invoices.

It consumes a REST API for data management (cf. [API NEGOSUD](cf. [API NEGOSUD](https://github.com/jsadaa/cube-api))).

## Installation

### Prerequisites

- PHP 8.2
- Symfony 7.0
- Symfony CLI
- Composer

### Installation

- Clone the repository

```bash
git clone git@github.com:LCE-CESI/webapp-symfony.git
```

- Install dependencies

```bash
composer install
```

- Configure the environment

Create a `.env.local` file at the root of the project and fill the API URL with the correct value.

Example:

```env
API_BASE_URL=http://localhost:5273/api
```


## Usage

- Start the Symfony server

```bash
php bin/console server:start
```

- Access the app in your browser

```
http://localhost:8000
```

## Authors

Léo Paillard