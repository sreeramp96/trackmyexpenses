<p align="center">
  <img src="public/android-chrome-512x512.png" width="128" height="128" alt="TrackMyExpenses Logo">
</p>

# <p align="center">TrackMyExpenses</p>
<p align="center">
  <i>The "finance-minimalist" dashboard for zero-clutter wealth management.</i>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat-square&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Filament-5.5-FFA200?style=flat-square&logo=laravel" alt="Filament 5.5">
  <img src="https://img.shields.io/badge/Tailwind-4.0-06B6D4?style=flat-square&logo=tailwindcss" alt="Tailwind 4.0">
  <img src="https://img.shields.io/badge/Livewire-4.0-FB70A9?style=flat-square&logo=livewire" alt="Livewire 4">
</p>

---

## 🎨 The Philosophy: Pure Clarity
TrackMyExpenses is a design-first financial tool. It is built on the principle of **High Information Density with Low Visual Noise**.

- **AMOLED Minimalism**: A true pitch-black (`#000000`) dark mode designed for high-contrast OLED screens.
- **Typography as Architecture**: Utilizing **Bricolage Grotesque** for UI personality and **IBM Plex Mono** for financial precision.
- **Semantic Color**: Color is never used for decoration—only for intent. `Green` for growth, `Red` for outflow, and `Amber` for alerts.
- **Native Experience**: A high-performance, single-page application feel powered by Livewire 4.

---

## 🚀 Key Innovations

### 📥 The Staging Import Workflow
Unlike traditional trackers, we use an **Extract-Transform-Load (ETL)** pattern for bank statements.
- **Unified Uploader**: Supports CSV, Excel, and PDF (HDFC ready).
- **Import Review Area**: A dedicated, editable staging table where you can live-edit, categorize, and validate transactions before they touch your main ledger.
- **Intelligent Categorization**: A memory-based engine that learns from your history to auto-assign categories to recurring vendors.

### 📊 Real-Time Wealth Dashboard
- **Dynamic Account Stats**: Granular KPI cards for every active account (Bank, CC, Cash) with real-time balance syncing.
- **Integrated Debt Engine**: Track money lent and borrowed with automated payment recording.
- **Zero-Clutter Tables**: High-contrast lists with advanced filtering, real-time search, and inline column editing.

---

## 🏗️ Technical Architecture
- **Observer Pattern**: Automatic account balance synchronization via `TransactionObserver`.
- **Service Layer**: Clean separation of business logic in `app/Services` to ensure controllers remain lean.
- **Audit Trails**: Complete change history for every transaction via **Spatie Activity Log v5**.
- **Soft Deletion**: Accidental deletions can be recovered; financial history is preserved.

---

## 🛠️ Modern Tech Stack
| Layer | Technology | Version |
| :--- | :--- | :--- |
| **Backend** | PHP | 8.4 |
| **Framework** | Laravel | 12.x |
| **Admin Panel**| Filament | 5.5.x |
| **Frontend** | Livewire | 4.2.x |
| **Styling** | Tailwind CSS | 4.2.x (Vite) |
| **Interactions**| Alpine.js | 3.15.x |

---

## 🏁 Getting Started

### 1. Installation
```bash
# Clone the repository and install dependencies
composer install
npm install

# Build assets with Vite (Tailwind v4)
npm run build

# Setup your environment
copy .env.example .env
php artisan key:generate

# Run migrations and seed foundational data
php artisan migrate --seed
```

### 2. Data Management
Refer to the [cleanup.md](cleanup.md) file for instructions on:
- Wiping demo/test data.
- Initializing original system categories.
- Re-seeding the demonstration environment.

### 3. Standards
- **Linter**: Always run `php artisan pint` before commits.
- **Logic**: No business logic in Controllers; use `app/Services`.
- **Components**: Use `x-panel`, `x-kpi-card`, and `x-badge` for consistent UI.

---
<p align="center">
  <i>Built for financial clarity and engineering excellence.</i>
</p>
