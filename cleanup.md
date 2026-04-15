# Data Cleanup & Initialization Guide

Follow these instructions to remove test data and restore original records in the TrackMyExpenses application.

## 1. Full Reset (Recommended)
To completely wipe the database and re-run all migrations, ensuring a clean slate:
```bash
php artisan migrate:fresh
```

## 2. Remove Test/Dummy Data Only
If you want to keep your structure but delete specifically generated dummy records:
```bash
php artisan tinker --execute "App\Models\Transaction::truncate(); App\Models\Debt::truncate(); App\Models\TemporaryTransaction::truncate();"
```

## 3. Add Original/Foundational Data
To load the essential categories and system-required data:
```bash
php artisan db:seed --class=CategorySeeder
```

## 4. Re-seeding Demo Data (Optional)
If you need to populate the dashboard for demonstration purposes again:
```bash
php artisan db:seed --class=DummyDataSeeder
```

---
**Note:** Always ensure you have a backup of your database before running `migrate:fresh` or `truncate` commands in a production environment.
