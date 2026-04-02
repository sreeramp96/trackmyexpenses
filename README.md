# TrackMyExpenses

A professional, minimalist, and high-performance expense tracking application built with the Laravel TALL stack (Tailwind, Alpine, Laravel, Livewire).

## Features

- **Personalized Dashboard:** Real-time KPI cards for Income, Expenses, Savings, and Budget utilization.
- **Transaction Management:** Detailed tracking of all income and expenses with category color indicators.
- **Account Tracking:** Manage multiple bank accounts, cash, and digital wallets with balance summaries.
- **Budget Planning:** Set monthly limits per category and track utilization with visual progress bars.
- **Debt Tracking:** Keep track of money lent or borrowed with overdue alerts and settlement status.
- **Data Export:** Export monthly reports as CSV for external analysis.

## Tech Stack

- **Framework:** [Laravel 12.x](https://laravel.com)
- **Frontend:** [Livewire](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev)
- **Styling:** [Tailwind CSS v3](https://tailwindcss.com) (Custom Minimalist Theme)
- **Typography:** IBM Plex Sans & IBM Plex Mono
- **Database:** MySQL (Local Development) / SQLite (Testing)

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js & NPM

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/trackmyexpenses.git
   cd trackmyexpenses
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install and compile assets:
   ```bash
   npm install
   npm run build
   ```

4. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=DummyDataSeeder
   ```

## Demo Credentials

You can log in to the demo account using the following credentials:
- **Email:** `sreeram@demo.com`
- **Password:** `password`

## Development Standards

This project follows strict Laravel best practices:
- **Service Layer:** All business logic is encapsulated in `app/Services`.
- **Minimalist Controllers:** Controllers only handle request routing and view responses.
- **TALL Stack:** Heavy use of Livewire for interactive components without leaving PHP.
- **Testing:** Comprehensive feature tests using PHPUnit with an in-memory database.
