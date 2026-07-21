# Modules

All `Admin/*` modules are tenant backoffice features, gated by the `access admin` Gate (`super admin`/`admin`/`manager`) and scoped to the current tenant via `BelongsToTenant`. `Platform/*` modules serve platform-staff operating the SaaS control plane across all tenants. Permission names are from `database/seeders/RolesPermissionsSeeder.php`.

---

# Catalog Management

## Purpose

Manages the product catalog backbone: attributes/variants, categories, and products themselves.

## Features

- CRUD attributes (e.g. Size, Color) and their values
- Category CRUD with image upload, slug auto-generation, parent/child tree with drag-and-drop reparenting, bulk activate/deactivate/delete, duplication
- Product CRUD with SKU auto-generation, per-attribute-combination variant generation, image/gallery management, bulk status/featured toggles, bulk delete, duplication, computed profit/profit-percentage

## User Stories

As an Admin,
I can define attributes like Size and Color
so that I can generate product variants from them.

As an Admin,
I can build a category tree and drag categories to reparent them
so that the storefront navigation reflects how I organize products.

As an Admin,
I can duplicate an existing product
so that I can create a similar listing without re-entering every field.

---

# Order Management & Manual Checkout

## Purpose

Handles orders placed through the storefront as well as manual/phone orders entered by staff.

## Features

- List/search/filter orders by status, payment status, date range
- Bulk and per-order status updates
- Manually create an order from the admin panel (routed through the same stock-locking service as POS)
- Edit advance payments on an order

## User Stories

As an Admin,
I can filter orders by status and date range
so that I can find and act on the orders that need attention.

As a Manager,
I can create an order manually for a phone or in-person customer
so that sales that don't happen through the storefront are still recorded and stock is deducted correctly.

---

# Point of Sale (POS)

## Purpose

A dedicated in-person/counter sale channel for cashiers, intentionally separate from `/admin` so cashiers get a focused full-screen terminal without backoffice access. Fully specified in [pos-module/SPEC.md](pos-module/SPEC.md) and [pos-module/UI_MODERNIZATION.md](pos-module/UI_MODERNIZATION.md).

## Features

- Product search/barcode scan-to-add, category rail, grid/list toggle
- Cart management: quantity, remove with undo, line notes, void transaction, variant picker
- Customer lookup/quick-create, coupon application
- Multi-tender split payments (cash/card/mobile banking/store credit) with change calculation
- Hold/resume sale, recently-sold strip, receipt print/reprint, keyboard shortcuts
- Shift management: open/close with opening float and cash-count variance, cash-in/cash-out during a shift
- Backoffice screens: register CRUD per warehouse, shift oversight (with force-close), refunds (full/partial, cash or store credit, restocks inventory), a standalone POS sales/cash/refunds dashboard

## User Stories

As a Cashier,
I can scan a barcode to add an item to the cart
so that I can process a sale quickly at the counter.

As a Cashier,
I can hold a sale and resume it later
so that I can serve another customer without losing an in-progress cart.

As a Manager,
I can close a cashier's shift and see the cash variance
so that I can reconcile the till at the end of a shift.

As a Manager,
I can refund a line item or a whole sale to cash or store credit
so that returns restock inventory and reverse the sale correctly.

**Deferred (documented, not implemented)**: tax, gift cards, loyalty points, offline mode, camera-based barcode scanning, real payment gateway integration, SMS/email receipts.

---

# Inventory & Warehousing

## Purpose

The stock backbone spanning warehouses, transfers, purchasing, suppliers, and physical counts, built on a shared lock-and-ledger pattern (`WarehouseStock` + append-only `StockMovement`).

## Features

- Per-product/warehouse stock table with low-stock/category/warehouse filters, manual adjustments (with batch/expiry tracking), stock-movement history per product
- ABC classification recompute, low-stock threshold settings
- Expiring-batch list and reorder suggestions (with one-click "create purchase order")
- Warehouse CRUD, "set default" warehouse
- Multi-line inter-warehouse stock transfers: create, receive (per-line confirmation), cancel
- Purchase orders against a supplier: create, receive (updates stock), cancel
- Supplier CRUD
- Cycle counts: start a count session, save progress, complete (reconcile counted vs. system quantity)

## User Stories

As a Manager,
I can see which products are low on stock across warehouses
so that I know what to reorder.

As an Admin,
I can transfer stock from one warehouse to another and confirm receipt
so that inventory records match what physically moved.

As a Manager,
I can receive a purchase order against a supplier
so that incoming stock is added to the right warehouse automatically.

As a Manager,
I can run a cycle count and reconcile counted vs. system quantities
so that stock records stay accurate over time.

---

# Marketing & Storefront Content

## Purpose

Merchandising and content tools for the public storefront: promotions, homepage content, and navigation.

## Features

- Coupon CRUD (percentage/fixed), activate/deactivate (single + bulk), validity windows — shared by storefront checkout and POS
- Reusable homepage/landing content blocks ("Sections") with drag-reorder, duplication, bulk toggle/delete
- Product-specific or campaign landing pages, with duplication
- Testimonial CRUD with drag-reorder and rating filter
- Navigation builder: nested menu items with drag-and-drop reparenting, header/footer "zone" component layout (move, resize, toggle visibility), quick "add category to navigation"
- Storefront category-grid display settings (columns, per-page defaults)

## User Stories

As an Editor,
I can create a percentage or fixed-amount coupon with a validity window
so that customers get a discount during a promotion, whether they check out online or at the register.

As an Admin,
I can drag homepage sections into a new order
so that I can control what customers see first without touching code.

As an Admin,
I can build the site's header and footer navigation by dragging items into place
so that the storefront menu matches my store's structure.

---

# Shipping & Fulfillment Settings

## Purpose

Configures delivery pricing for the storefront.

## Features

- Tenant-wide shipping settings
- Per-city rate CRUD, plus a catch-all "rest of all cities" rate

## User Stories

As an Admin,
I can set a shipping rate for a specific city and a fallback rate for everywhere else
so that checkout shows the correct delivery cost for any customer.

---

# Website Settings & Storefront Configuration

## Purpose

Single-purpose settings screens governing the tenant's public storefront appearance and metadata.

## Features

- General, Appearance (theme/colors), Hero, Header, Footer, Product Grid, Product Details, Category Grid, SEO, Social, Contact, Localization (currency/locale), Analytics (tracking scripts), Custom Code (HTML/JS/CSS injection), Domains (custom domain management), Verification

## User Stories

As an Admin,
I can pick a hero style and configure its content
so that my storefront homepage matches my brand without custom development.

As an Admin,
I can add and verify a custom domain for my store
so that customers reach my store at my own domain instead of a subdomain.

---

# User Access Control

## Purpose

Manages who can log into the tenant backoffice and what they can do there.

## Features

- User CRUD with role assignment, search/filter by role
- Role CRUD with permission assignment via checkbox groups
- Raw permission CRUD (escape hatch; permissions are otherwise seeded)

## User Stories

As an Admin,
I can create a staff account and assign it a role
so that new employees get exactly the access their job requires.

As a Super Admin,
I can create a custom role with a specific set of permissions
so that I can match access to my store's own staffing structure.

---

# Reporting & Analytics Dashboard

## Purpose

Read-heavy reporting surface for tenant staff, built on a shared dashboard base class with per-user card layout preferences.

## Features

- Overview, Sales, Products, Customers, Orders pages with configurable KPI/chart/insight cards
- Inventory report (stock levels with search/category/stock filters)
- Profitability report (margin breakdown per product/order)

## User Stories

As a Manager,
I can view a sales dashboard filtered by date range
so that I can track performance over any period I choose.

As an Admin,
I can rearrange which metric and chart cards appear on my dashboard
so that the reporting view matches what I care about most.

---

# Platform Administration (SaaS Control Plane)

## Purpose

The platform operator's console for running the SaaS business across all tenants — distinct from any single tenant's admin panel.

## Features

- Tenant CRUD, plan/subscription updates, suspend/reactivate, custom domain management, tenant impersonation, manual payment recording
- Plan CRUD with currency switch, reordering, default-plan selection
- Upgrade-request approval/rejection (paired with the tenant-side request flow in Billing)
- Platform-wide billing-events ledger with CSV export
- Platform-wide usage/revenue analytics
- Platform settings and website defaults that seed new tenants' storefront settings

## User Stories

As Platform Staff,
I can suspend a tenant whose subscription has lapsed
so that their store stops serving traffic until payment resumes.

As Platform Staff,
I can impersonate a tenant's account
so that I can troubleshoot an issue exactly as they see it.

As Platform Staff,
I can approve or reject a tenant's plan-upgrade request
so that plan changes go through a controlled approval step.

As a Tenant Admin,
I can request an upgrade to a higher plan from my Billing page
so that I can unlock features without contacting support directly.

---

# Account Settings

## Purpose

Personal account management available to any authenticated user (tenant staff or platform staff), not gated by business permissions.

## Features

- Profile update, email re-verification
- Password change
- Two-factor authentication setup/disable, recovery codes
- Theme (appearance) preference
- Personal Facebook Pixel setting
- Self-service account deletion

## User Stories

As any authenticated user,
I can enable two-factor authentication on my account
so that my login is protected even if my password is compromised.
