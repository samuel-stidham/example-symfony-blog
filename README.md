# Symfony Blog

A simple blog platform built with **Symfony 7**, **Doctrine ORM**, **Twig**, and **TailwindCSS**.
This project demonstrates authentication, authorization, CRUD operations, and modern Symfony best practices.

It includes seeded dummy data and PHPUnit tests for key routes, controllers, and database interactions.

## Features

-   User authentication via Symfony Security (`LoginFormAuthenticator`)
-   Authorization using `#[IsGranted('ROLE_USER')]` attributes
-   Blog post creation, editing, and deletion
-   Slug-based routing for clean URLs
-   TailwindCSS styling for a clean modern UI.
-   The UI is intentionally minimal—prioritizing functionality and clean code over polish given the timebox.
-   Fully tested with PHPUnit and Doctrine’s in-memory SQLite database
-   File-backed SQLite (`var/test.db`) for stable integration testing
-   Beautiful home and post pages using Twig templates

## Requirements

-   PHP 8.2+
-   Composer
-   Symfony CLI (recommended)
-   Node.js & npm (for Tailwind CSS)
-   SQLite or other supported database

## Setup

1. Install dependencies

```bash
composer install
npm install
npm run build:css
```

2. Configure your environment

```bash
cp .env .env.local
```

⚠️ **Never commit .env, .env.local, or .env.test files to source control. See the below disclaimer for why I included them in this repository.**

3. Initialize the database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

4. Run the local server or run using Laravel Valet or Laravel Herd. I used Laravel Valet for Linux.

```bash
symfony serve

```

5. Run tests

```bash
php bin/phpunit
```

---

## Testing

The repository includes complete feature tests for:

-   Authentication (`LoginTest`)
-   Routing (`SlugRoutingTest`)
-   Controllers (`HomeControllerTest`, `PostControllerTest`)
-   Database lifecycle management (`DatabaseTestCase`)

Each test uses a file-backed SQLite database (`var/test.db`) recreated before every run.

---

## Frontend

This project uses **TailwindCSS** built via Webpack Encore.

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

CSS is compiled to:

```bash
public/build/app.css
```

---

## 🔒 Disclaimer

This repository is **for educational and portfolio purposes only**.
It uses **dummy data** and a **non-production SQLite database.**
No real user data or credentials should ever be used here.

`.env`, `.env.local`, and `.env.test` files **must never be committed** to version control.
They can expose sensitive environment variables or secrets.

---

## Author

Samuel Stidham
Software Engineer | SNHU Computer Science
