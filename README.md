# CivicEase

Laravel 12 issue reporting platform for local community problems.

## Stack
- Laravel 12
- Blade
- Laravel Breeze
- Tailwind CSS
- Alpine.js
- Vite
- SQLite
- Leaflet.js
- Postcodes.io

## Features
- Public marketing pages: Home, About, Privacy, Accessibility
- Resident flow: register, login, dashboard, report issue, track reports, profile management
- Admin flow: dashboard, report review, status updates with audit history
- Community map with Leaflet markers
- UK postcode lookup and map pin placement
- Optional image uploads for reports
- Basic feature coverage for auth, report submission, access control, and admin updates

## Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run dev
php artisan serve
```

## Notes
- Default local database is SQLite at `database/database.sqlite`.
- Leaflet assets are loaded from the Leaflet CDN.
- Postcode lookup uses `https://api.postcodes.io`.

## Seeded users
- Admin: `admin@example.com` / `Admin123!`
- Resident: `resident@example.com` / `Password123!`
