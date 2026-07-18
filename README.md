# Laravel + Livewire Starter Kit

## Introduction

Our Laravel + [Livewire](https://livewire.laravel.com) starter kit provides a robust, modern starting point for building Laravel applications with a Livewire frontend.

Livewire is a powerful way of building dynamic, reactive, frontend UIs using just PHP. It's a great fit for teams that primarily use Blade templates and are looking for a simpler alternative to JavaScript-driven SPA frameworks like React and Vue.

This Livewire starter kit utilizes Livewire 3, Laravel Volt (optionally), TypeScript, Tailwind, and the [Flux UI](https://fluxui.dev) component library.

If you are looking for the alternate configurations of this starter kit, they can be found in the following branches:

- [components](https://github.com/laravel/livewire-starter-kit/tree/components) - if Volt is not selected
- [workos](https://github.com/laravel/livewire-starter-kit/tree/workos) - if WorkOS is selected for authentication

## Docker Setup

This project includes Docker support with PostgreSQL. The Docker setup includes:

- **PostgreSQL 16** - Database service (port 5432)
- **PHP 8.3 FPM** - Application service with all required extensions
- **Nginx** - Web server (port 8000 by default)

### Prerequisites

- Docker and Docker Compose installed
- Copy `.env.example` to `.env` and configure your database settings

### Getting Started

1. Build and start the containers:
```bash
docker-compose up -d
```

The Laravel application will be available at **http://localhost:8000** (or the port specified in `APP_PORT` environment variable).

2. Install dependencies:
```bash
docker-compose exec app composer install
```

3. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

4. Run migrations:
```bash
docker-compose exec app php artisan migrate
```

5. Seed the database with dummy data:
```bash
docker-compose exec app php artisan db:seed
```

### Seeded Data

The database seeders include the following dummy data:

- **Roles & Permissions**: Super Admin and Admin roles with comprehensive permissions
- **Admin Users**: 
  - Super Admin: `superadmin@example.com` / `password`
  - Admin: `admin@example.com` / `password`
- **Cities**: 15 major cities in Bangladesh (Dhaka, Chittagong, Sylhet, etc.)
- **Categories**: 6 product categories (Date Molasses, Natural Sweeteners, Honey Products, Organic Foods, Health Supplements, Spices & Herbs)
- **Products**: 9 products across different categories with descriptions in English and Bengali
- **Attributes**: Weight, Color, and Size attributes with predefined values
- **Coupons**: 6 discount coupons (percentage and fixed discounts)
- **Orders**: 8 sample orders with various statuses (pending, confirmed, processing, shipped, delivered, cancelled)
- **Order Items**: Multiple order items across different orders
- **Testimonials**: 6 customer testimonials with ratings
- **Landing Page Sections**: Pre-configured landing page sections
- **Navigation Items**: Menu navigation items

### Ports

- **Laravel Application**: http://localhost:8000 (configurable via `APP_PORT` in `.env`)
- **PostgreSQL**: localhost:5432 (configurable via `DB_PORT` in `.env`)

### Useful Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Run artisan commands
docker-compose exec app php artisan <command>

# View logs
docker-compose logs -f

# View logs for specific service
docker-compose logs -f nginx
docker-compose logs -f app

# Access container shell
docker-compose exec app bash
```

The project is running in Docker with PostgreSQL, and all migrations have been applied.

## Official Documentation

Documentation for all Laravel starter kits can be found on the [Laravel website](https://laravel.com/docs/starter-kits).

## Contributing

Thank you for considering contributing to our starter kit! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## License

The Laravel + Livewire starter kit is open-sourced software licensed under the MIT license.
