# Laravel + Livewire E-Commerce Platform

## Project Summary

A multi-tenant e-commerce platform. A single codebase and deployment serves both the **platform** (central marketing site + platform-staff console for managing tenants, plans, and billing) and per-tenant **stores** (storefront + admin dashboard + POS), reached by subdomain (`{slug}.{central-domain}`) or a verified custom domain.

## Purpose

Let a platform operator run many independent online stores from one Laravel install: each tenant gets its own catalog, orders, inventory, point-of-sale terminal, and storefront design, isolated by tenant-scoped queries rather than separate databases.

## Tech Stack

- **Backend**: PHP 8.3+ (Docker image: `php:8.4-fpm-alpine`), Laravel 13
- **Frontend**: Livewire 3 (`livewire/flux` + `livewire/volt`), Flux UI, Tailwind CSS 4 (CSS-first config, no `tailwind.config.js`), Vite 7, Chart.js, SortableJS
- **Auth & permissions**: Laravel Fortify, `spatie/laravel-permission` (teams mode, tenant-scoped)
- **Database**: PostgreSQL 18
- **Testing**: Pest 4
- **Infra**: Docker Compose — Postgres, PHP-FPM app container, Nginx (plain HTTP), Caddy (TLS termination for tenant custom domains)

## Main Features

- Multi-tenant storefront + admin dashboard, resolved by domain/subdomain
- Product catalog: categories, attributes/variants, products
- Orders (storefront + manual/admin-entered)
- Point of Sale (POS): terminal, shifts/cash drawer, refunds, held sales
- Inventory & warehousing: multi-warehouse stock, stock transfers, purchase orders, suppliers, cycle counts, ABC classification
- Marketing/content: coupons, landing pages, homepage sections, testimonials, navigation builder
- Shipping rate configuration (per-city)
- Storefront appearance & SEO settings
- Reporting dashboard (sales, orders, customers, products, inventory, profitability)
- User/role/permission management per tenant
- Platform console: tenant lifecycle, plans, billing ledger, upgrade requests, platform-wide analytics

See [modules.md](modules.md) for details.

## Folder Structure

```
app/
  Actions/Fortify/     Fortify auth actions
  Console/Commands/    Scheduled artisan commands
  Http/Middleware/     Tenant resolution, locale, route guards
  Livewire/
    Admin/             Tenant backoffice (catalog, orders, POS, inventory, settings...)
    Platform/          Platform-staff console (tenants, plans, billing, analytics)
    Pos/               POS terminal (cashier-facing, not under Admin/)
    Dashboard/          Reporting
    Settings/          Personal account settings
  Models/              Eloquent models (tenant-scoped via BelongsToTenant)
  Observers/           Stock/order audit trail
  Services/            POS sale/refund, shipping calculation
  Support/Tenancy.php  Current-tenant helper
resources/views/
  components/layouts/  app, platform, public, pos, auth layouts
  components/x-*       Shared component kits (admin, platform, public, pos, dashboard)
  livewire/            Blade views, mirrors app/Livewire/ 1:1
routes/web.php         Central-domain + tenant-domain route groups
database/migrations/   Schema (tenancy, catalog, orders, POS, inventory, content)
database/seeders/      Demo/default data
docker/                Caddy, nginx, php config
tests/                 Pest feature/unit tests
docs/                  This documentation + docs/pos-module/ (POS spec)
```

## How to Run

Prerequisite: Docker and Docker Compose.

```bash
cp .env.example .env
docker compose up -d
docker exec Laravel_app composer install
docker exec Laravel_app php artisan key:generate
docker exec Laravel_app php artisan migrate
docker exec Laravel_app php artisan db:seed
```

Visit `http://localhost:8000` (platform) or `http://default.localhost:8000` (seeded tenant, slug `default`).

Artisan commands must run inside the `Laravel_app` container — `DB_HOST=postgres` only resolves inside the Docker network.

Seeded logins (all `/password`): `platformadmin@example.com` (platform), `admin@example.com` / `superadmin@example.com` (tenant `default`, login on `default.localhost:8000`, not the central domain).

## Environment Variables (reference only)

Standard Laravel vars (`APP_*`, `DB_*`, `SESSION_*`, `CACHE_*`, `QUEUE_*`, `MAIL_*`) plus:

| Variable | Purpose |
|---|---|
| `CENTRAL_DOMAINS` | Comma-separated hosts treated as the platform, not a tenant subdomain |
| `PLATFORM_DOMAIN` | Primary domain Caddy serves directly / tenant CNAMEs point at |
| `TENANT_DOMAIN_TARGET` | Hostname a tenant's custom-domain CNAME/A-record must resolve to for verification |
| `FACEBOOK_PIXEL_ID` | Default Facebook Pixel ID (`config/services.php`) — not in `.env.example`, add if used |

Full list: `.env.example`. Mail can be routed through Postmark, Resend, or AWS SES (`config/services.php`); no SMS or payment-gateway integration exists — order payments are recorded, not processed.

## Useful Commands

```bash
# Local dev (outside Docker, if PHP/Node are installed locally)
composer setup   # install, .env, key:generate, migrate, npm install/build
composer dev     # serve + queue:listen + vite dev, concurrently
composer test    # config:clear + artisan test

# Frontend build (no Vite dev server in this deployment; nginx serves static public/build)
npm run build
npm run dev

# Docker
docker compose up -d / down
docker exec Laravel_app php artisan <command>
docker compose logs -f

# Custom artisan commands
php artisan tenants:verify-domains         # scheduled every 5 min
php artisan platform:notify-trial-ending   # scheduled daily
php artisan inventory:recompute-abc        # scheduled daily
```
