# Changelog

## Unreleased

### Added

- Point of Sale (POS) module: cashier terminal, registers, shifts with cash-drawer variance tracking, held sales, refunds, customer records, split payments — see [pos-module/SPEC.md](pos-module/SPEC.md)
- Barcode fields on products and product attributes

### Changed

- Order model/admin panel updated to support POS-originated orders (payments, refunds)

---

## 2026-07-21

### Added

- Multi-warehouse inventory management system (warehouses, warehouse stock, stock movements, transfers, purchase orders, suppliers, cycle counts)

### Removed

- Unused ingredients/benefits fields from the product catalog

## 2026-07-20

### Added

- Platform-wide currency selection for plan pricing
- Platform notifications and emails (tenant lifecycle/billing events)

### Changed

- Reserved the central domain for the platform frontend
- Overhauled admin panel design consistency across all modules

## 2026-07-19

### Added

- Footer, Category Grid, Product Grid, and Header storefront variant systems
- 10 selectable storefront hero styles with a per-tenant admin picker
- Self-service tenant signup

### Changed

- Isolated the test database
- Redesigned the product Add/Edit admin page with a two-column layout

## 2026-07-18

### Added

- Multi-tenant platform layer with domain management
- Per-section website settings admin pages, categories display settings
- Split admin dashboard into focused subpages

### Fixed

- Auth redirect handling for the multi-tenant platform layer

### Changed

- Rewrote README to reflect the multi-tenant platform setup

## 2026-07-18 — Initial commit

Initial Laravel + Livewire starter kit.
