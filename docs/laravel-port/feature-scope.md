# Laravel Port Feature Scope

This document classifies features for the Laravel port of `restotech/standard`. It is the working boundary between Standard Edition behavior, Full Edition extension points, and deferred implementation slices.

## Evidence Sources

- Standard Yii module in this repository.
- Standard SQL reference: `docs/restotech.sql`.
- Standard docs under `docs/`.
- Full Yii module at `../restotech-full`.
- Full docs, especially `docs/domain-and-flows.md` and `docs/appendix-controller-index.md`.

## Scope Rules

1. If a behavior is implemented only in `restotech-full`, classify it as a Full Edition feature by default.
2. If Standard contains models or tables but no Standard UI/controller workflow, treat that as data readiness or dependency support, not Standard behavior.
3. If Standard checkout/payment code directly uses a behavior, classify that behavior as Standard even if Full contains helper validation screens or richer CRUD.
4. If classification is uncertain, mark it as `Needs mapping` until the relevant Yii source is inspected.
5. For the Laravel port, Back Office CRUD ownership is defined by the minimum setup needed to operate Standard POS, not by exact Yii Standard controller parity. Laravel Standard may add basic CRUD where Yii Standard only provided init/support screens; richer workflows remain Full Edition or deferred unless explicitly classified as Standard.

## Standard Edition Scope

These features belong to `restotech/standard` Laravel unless later evidence contradicts them.

### Package and Platform

- Pure Laravel package named `restotech/standard`.
- Namespace `Restotech\Standard`.
- Laravel-native, behavior-compatible port.
- English Laravel-conventional schema and code language.
- Fresh Laravel installs only.
- MariaDB target.
- Host Laravel authentication with Restotech-specific authorization.
- Single-tenant restaurant/business per Laravel app.

### Point of Sale Core

- Tablet-oriented POS SPA bundled in the package.
- Internal POS JSON endpoints separate from public API.
- Transaction day hard-gates POS mutations.
- Active cashier session required for POS mutations.
- Dining table session lifecycle:
  - open table,
  - add order items,
  - change quantities,
  - add notes,
  - void order items,
  - mark free/complimentary items,
  - apply item/menu discounts,
  - apply bill/session discounts,
  - print/generate bill,
  - unlock bill before payment,
  - checkout/payment,
  - close table session.
- One table session produces one sales invoice.
- Multiple payment methods on one sales invoice.
- Bill print locks order changes.
- Checkout requires bill print/generation by default, configurable for quick-service mode.
- Customer bill/receipt print and reprint through a printing abstraction.
- Browser/manual print first; physical/network printer drivers later.
- Simple POS table selection by dining area/table is Standard first-slice scope; rich visual layout and opened-table dashboards are deferred/Full-adjacent.

### Payments

- Cash and ordinary payment methods.
- Accounts Receivable payment is Standard: first-slice checkout may record a receivable payment method when configured, while receivable collection screens and reports are deferred Standard work.
- Employee Credit payment, because Standard checkout handles `XLIMIT` behavior.
- Voucher payment, because Standard checkout handles `XVCHR` behavior.
- Minimal Voucher validation/redemption service for POS checkout, because Standard checkout marks vouchers as used.
- Employee Credit validation and minimal administration are Standard scope: Standard checkout decrements employee remaining credit, Standard Employee Back Office maintains credit limit/remaining balance, and Laravel Standard should own the POS validation service even though Yii Full supplied a validation endpoint.

### Sales, Inventory, and Stock

- Checkout creates sales invoice, invoice items, payments, and closes the table session inside one database transaction.
- Menu recipe stock consumption occurs synchronously during checkout.
- Stock is tracked by item SKU, storage location, and optional rack.
- Negative stock is configurable; default is not allowed.
- No stock reservation visibility in the first slice.
- Direct Purchase is Standard scope as a simple stock-entry workflow, because Yii Standard implements create/update/delete/print and stock mutation; it is deferred out of the first POS checkout slice.

### Back Office Foundation

- Internal reusable Back Office CRUD foundation.
- Minimal Blade components.
- Theme adapter system with only `minimal` implemented initially.
- Publishable/overridable Back Office views.
- Back Office CRUD screens required for operating Standard POS are Standard scope.
- First-slice Back Office CRUD/setup covers: Employee; Restotech user profile, roles, permissions, and role permissions; Transaction Day; Shift; Cashier Balance; Dining Area; Dining Table basic CRUD without rich layout design; Menu Category; Menu Unit; Menu Item; Menu Condiment; Menu Recipe Item; Inventory Category; Inventory Item; Inventory SKU; Storage Location; Storage Rack; Payment Method; tax/service/settings; and Number Sequence setup.
- First-slice Back Office excludes: Voucher issuance/admin screens; Printer and Menu Category Printer screens; Direct Purchase; Supplier/procurement/payables; accounts receivable collection screens; manual stock movement/correction/transfer screens; and reports beyond test/dev verification.

### Operations and Settings

- Restaurant-wide transaction day.
- Shifts.
- Cashier balances.
- Global tax and service charge settings.
- Table-level tax/service charge exemptions.
- Tax/service charge snapshot at table open.
- Number sequences with row locking.
- Localization with English and Indonesian UI text where practical.

### Public API

- Public API v1 exists eventually under `/api/restotech/v1`.
- Public API is implemented after POS internal API and shared services.
- API auth is middleware-configurable; Sanctum documented but not required.

## Full Edition Extension Points

These are implemented or strongly evidenced in the Yii `restotech-full` module and should not be built into Laravel Standard unless reclassified later.

### POS Full Features

- Split bill/order.
- Join tables.
- Transfer table.
- Transfer menu/items between sessions.
- Kitchen/menu queue:
  - queue order items,
  - mark queue finished,
  - mark queue sent.
- Table booking/reservation:
  - list bookings,
  - create booking,
  - open table from booking.
- Payment correction / invoice correction workflow.
- Cash drawer action.
- Opened table overview and richer room/table layout workflows beyond basic Standard table selection, including drag/drop layout coordinates, table shapes/images, multi-session table selection, join/transfer/split flows, and booking-driven table opening.

### Back Office Full Features

The Yii Full module provides actual controller actions and views for several areas where Standard often only has models or shell controllers. Treat these as Full candidates until mapped:

- Purchase orders.
- Supplier delivery / receiving.
- Supplier delivery invoices and payable payments.
- Purchase returns.
- Manual stock inflow/outflow/transfer.
- Stock conversion.
- Stock correction / opname verification.
- Stock movement conversion/report views.
- Finance activity report.
- Sale invoice refund/correction screens.
- Voucher Back Office CRUD and voucher issuance/management screens. Standard supports voucher payment/redemption, but Yii Full owns voucher administration screens.
- Richer room/table layout administration beyond Standard operational needs.

## Deferred Standard Slices

These may still be Standard eventually, but are not first-slice work.

- Public API parity.
- Report export parity.
- Printer and Menu Category Printer Back Office configuration screens as Standard-deferred configuration.
- Physical printer drivers.
- Kitchen/order printer automation, which may be supplied by a later Full/kitchen extension using the deferred printer configuration.
- Accounts Receivable collection screens, receivable payment records, and receivable reports as deferred Standard work.
- Direct Purchase Back Office workflow.
- Rich procurement/payables remain Full Edition candidates: purchase orders, supplier delivery/receiving, supplier invoices, payable payments, purchase returns, and supplier administration.

## First Laravel Implementation Slice

The first implementation slice should prove the Standard POS checkout path:

1. Package scaffold, config, service provider, routes, migrations, seeders, and tests.
2. Core POS models and settings needed for table checkout.
3. Transaction day and cashier session gates.
4. Dining table session open/order/discount/bill/checkout flow.
5. Multiple payment methods including Employee Credit, Voucher, and Accounts Receivable if the data model is ready/configured.
6. Synchronous recipe-based stock mutation on checkout.
7. Sales invoice and receipt generation.
8. Pest/Testbench integration tests against MariaDB.

## Needs Mapping

No first-slice scope ownership questions remain. Before coding later deferred areas, inspect the relevant Standard and Full Yii source and update this section.
