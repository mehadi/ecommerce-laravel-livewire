# Laravel + Livewire E-Commerce Platform

## Introduction

A multi-tenant e-commerce platform built with Laravel and [Livewire](https://livewire.laravel.com). A single codebase and deployment serves:

- **The platform** — a central marketing site and platform-staff admin (tenant management, plans, billing, platform-wide analytics), reachable on the app's central domain(s).
- **Tenant stores** — each tenant gets their own storefront, admin dashboard, and optionally a custom domain, reached via a subdomain (`{slug}.{central-domain}`) or a verified custom domain.

Built with Livewire 3, TypeScript, Tailwind, and [Flux UI](https://fluxui.dev), on PostgreSQL.

## How to Use This Project

### Prerequisites

- Docker and Docker Compose

### 1. Set up the environment

```bash
cp .env.example .env
```

The defaults work out of the box for local development (`CENTRAL_DOMAINS=localhost,127.0.0.1`, `PLATFORM_DOMAIN=localhost`). Leave `TENANT_DOMAIN_TARGET` blank unless you're testing real custom-domain verification against a public domain you control — see [docker/caddy/Caddyfile](docker/caddy/Caddyfile) for how it's used.

### 2. Build and start the containers

```bash
docker compose up -d
```

This starts four services:

| Container            | Purpose                                                             |
|----------------------|----------------------------------------------------------------------|
| `Laravel_postgres`   | PostgreSQL 18 database (port `5432`)                                 |
| `Laravel_app`        | PHP-FPM application container                                        |
| `Laravel_nginx`      | Serves the app over plain HTTP inside the docker network (port `8000` on the host) |
| `Laravel_caddy`      | Terminates TLS for tenant custom domains/subdomains (ports `80`/`443`) — only relevant once you have real domains pointed here |

(Container name prefix follows `APP_NAME` in `.env`, `laravel` by default.)

### 3. Install dependencies and prepare the app

```bash
docker exec Laravel_app composer install
docker exec Laravel_app php artisan key:generate
docker exec Laravel_app php artisan migrate
docker exec Laravel_app php artisan db:seed
```

> Artisan commands must run **inside** the `Laravel_app` container (`docker exec Laravel_app php artisan ...`) — the database host `postgres` only resolves from within the docker network, so running `php artisan` directly on your host will fail to connect.

### 4. Log in

Visit **http://localhost:8000**:

- **Platform admin** (manage tenants, plans, billing): `platformadmin@example.com` / `password` — lands on `/platform` after login.
- **Tenant admin** (manage the seeded "Default Store" tenant): `admin@example.com` or `superadmin@example.com` / `password` — but log in on the tenant's own host, not the central domain (see below), so it lands on `/dashboard` instead of 404ing.

### Accessing a tenant store locally

The seeded `DefaultTenantSeeder` creates a tenant with slug `default`, reachable at:

```
http://default.localhost:8000
```

(Any `{slug}.localhost` subdomain resolves without extra `/etc/hosts` entries on most systems/browsers.) This is where you log in as `admin@example.com` to reach that tenant's storefront and `/dashboard`.

Logging in with tenant credentials on the bare `http://localhost:8000` central domain — or hitting `/dashboard` there directly — won't work: that host has no tenant resolved, so tenant-only routes don't exist there. Platform staff hitting the same routes get redirected back to `/platform` automatically instead of seeing a 404.

### Seeded Data

- **Platform**: one platform-staff account (`platformadmin@example.com`), a `default` plan set (Starter/Growth/Pro), and one tenant (`Default Store`, slug `default`) that owns all of the demo data below.
- **Tenant admin users**: Super Admin (`superadmin@example.com`) and Admin (`admin@example.com`), both `/password`, with Spatie roles & permissions scoped to the `default` tenant.
- **Cities**: 15 major cities in Bangladesh (Dhaka, Chittagong, Sylhet, etc.)
- **Categories**: 6 product categories (Date Molasses, Natural Sweeteners, Honey Products, Organic Foods, Health Supplements, Spices & Herbs)
- **Products**: 9 products across categories, with descriptions in English and Bengali
- **Attributes**: Weight, Color, and Size, with predefined values
- **Coupons**: 6 discount coupons (percentage and fixed)
- **Orders**: 8 sample orders across order statuses, with order items
- **Testimonials**, **landing page sections**, and **navigation items**: pre-configured demo content

### Ports

- **Laravel Application (HTTP, dev)**: http://localhost:8000 (configurable via `APP_PORT` in `.env`)
- **Caddy (TLS, for real domains)**: 80/443 on the host
- **PostgreSQL**: localhost:5432 (configurable via `DB_PORT` in `.env`)

### Useful Commands

```bash
# Start / stop containers
docker compose up -d
docker compose down

# Run artisan commands
docker exec Laravel_app php artisan <command>

# View logs
docker compose logs -f
docker logs Laravel_app
docker logs Laravel_caddy

# Access a container shell
docker exec -it Laravel_app bash
```

## Official Documentation

This project is built on the [Laravel Livewire starter kit](https://laravel.com/docs/starter-kits); general Laravel/Livewire documentation applies for anything not covered above.
