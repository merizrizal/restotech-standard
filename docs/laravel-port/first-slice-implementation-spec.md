# First Slice Implementation Spec: Laravel Standard POS Checkout

**Source:** `docs/laravel-port/feature-scope.md`, `docs/laravel-port/model-mapping.md`, ADRs `0001`-`0005`, and the resolved scope-grilling session.

**Goal:** Create the first compile-safe Laravel package slice for `restotech/standard` that proves the Standard POS checkout path from package bootstrapping through table session checkout, without implementing deferred Back Office, Full Edition, or rich reporting workflows.

---

## I. Overview and Contract

The first slice ports Restotech Standard as a Laravel-native package, not as a Yii schema clone. It should establish the package scaffold, service provider, config, routes, migrations, core models, Pest/Testbench/MariaDB test harness, and the minimum POS checkout workflow.

### In-scope first-slice capabilities

- Composer package scaffold for `restotech/standard` under namespace `Restotech\Standard`.
- Laravel package service provider with publishable config, migrations, views, translations, and assets as needed.
- Configurable route groups for:
  - Back Office: `/restotech/admin`
  - POS UI/internal endpoints: `/restotech/pos`
  - Public API placeholder prefix: `/api/restotech/v1`
- Pest + Orchestra Testbench setup.
- Docker-based MariaDB 12.3.2 integration-test support.
- Core migrations/models for first-slice POS checkout.
- Minimal Back Office CRUD/setup for POS-operating data only.
- POS checkout service path:
  - transaction day gate,
  - cashier balance gate,
  - open table session,
  - add/change/void/free/discount order items,
  - generate/print bill lock,
  - unlock bill,
  - checkout with multiple payment methods,
  - Employee Credit debit,
  - Voucher validation/redemption,
  - optional Accounts Receivable payment recording,
  - synchronous recipe stock consumption,
  - sales invoice + receipt generation.

### Out of scope for first slice

- Voucher issuance/admin screens.
- Printer and Menu Category Printer Back Office screens.
- Physical/network printer drivers.
- Kitchen/order printer automation.
- Direct Purchase Back Office workflow.
- Supplier/procurement/payables.
- Accounts Receivable collection screens and reports.
- Manual stock movement/correction/transfer screens.
- Reports beyond test/dev verification.
- Public API parity beyond placeholder routing/contracts.
- Full Edition workflows: split bill/order, join/transfer table, transfer menu, booking, kitchen queue, payment/invoice correction, cash drawer action, opened-table dashboard, rich room/table visual layout.

---

## II. Observed Evidence and Assumptions

### Observed evidence

- `CONTEXT.md` defines canonical language: Standard Edition, Full Edition, Point of Sale, Back Office, Dining Table, Table Session, Sales Invoice, Employee Credit, Voucher, Accounts Receivable.
- `docs/adr/0001-laravel-native-behavior-compatible-port.md` chooses a Laravel-native behavior-compatible port with English schema/code language and `restotech_` physical table prefix.
- `docs/adr/0002-standard-package-with-full-edition-extension-points.md` keeps Full-only features as extension points.
- `docs/adr/0003-pos-vue-spa-and-back-office-blade.md` chooses Vue 3 + Vite for POS and Blade/minimal JS for Back Office.
- `docs/adr/0004-shared-services-for-business-logic.md` requires services/actions shared by POS internal endpoints and public API.
- `docs/adr/0005-internal-back-office-crud-foundation.md` chooses an internal Back Office CRUD foundation.
- `docs/laravel-port/feature-scope.md` now states no first-slice scope ownership questions remain.
- `docs/laravel-port/model-mapping.md` lists the first-slice model set and Back Office CRUD boundary.
- Existing `composer.json` is a Yii2 extension (`synctech/restotech-standard`) and must be replaced or transformed for the Laravel package.

### Assumptions to verify in Chunk 0

- Current Laravel major version and compatible `orchestra/testbench` version.
- Minimum PHP version for the target Laravel major.
- Whether the repository will be rewritten in-place or staged as a new package scaffold in this same repository.
- Whether Vue/Vite assets are included as first-slice stubs or deferred until after backend service tests pass.

---

## III. Required Technical Dependencies and Imports

### Runtime/package dependencies

Conceptual dependencies, versions to verify before implementation:

- `illuminate/support` and relevant Laravel components for package support.
- `orchestra/testbench` compatible with target Laravel major.
- `pestphp/pest` and Laravel Pest plugin if needed.
- MariaDB 12.3.2 for integration tests.
- Vue 3 + Vite for POS SPA shell when UI chunk begins.

### Package conventions

- Namespace: `Restotech\Standard`.
- Package name: `restotech/standard`.
- Package-owned physical tables: `restotech_*`.
- Host auth table remains host-owned: `users`.
- Host Laravel auth is authoritative; package stores Restotech-specific user profile/roles/permissions.

---

## IV. Step-by-Step Procedure / Execution Flow

### Package boot flow

1. Composer autoload registers `Restotech\Standard\`.
2. Service provider merges config.
3. Service provider loads package migrations, routes, views, translations, and commands as needed.
4. Host app opts into routes/config publishing.
5. Testbench boots the package with MariaDB test config.

### POS checkout flow

1. POS request reaches package route under `/restotech/pos`.
2. Middleware/gates verify host-authenticated user, open Transaction Day, and active Cashier Balance.
3. Service opens or retrieves a Table Session for a Dining Table.
4. Service adds Order Items and applies quantity/note/void/free/discount changes.
5. Bill generation marks the Table Session as bill-locked.
6. Checkout validates bill lock unless quick-service config bypass is enabled.
7. Checkout creates Sales Invoice and Sales Invoice Items.
8. Checkout records one or more Sales Invoice Payments:
   - cash/ordinary payment methods,
   - Employee Credit debit against remaining employee credit,
   - Voucher validation and redemption,
   - optional Accounts Receivable payment method recording.
9. Checkout consumes recipe stock synchronously and writes Stock Movement records.
10. Checkout closes/marks paid the Table Session.
11. Receipt/bill output is generated through a printing abstraction; first slice uses browser/manual print output.
12. All checkout mutations occur inside one database transaction.

---

## V. Failure Modes and Resilience

| Stage | Failure Mode | Agent/System Action | Next State/Error Report |
|---|---|---|---|
| Package boot | Service provider not discovered/registered | Add package provider registration and Testbench provider config | Testbench boot failure with provider evidence |
| Config | Missing route prefix/config value | Use defaults from package config | Deterministic default route/config behavior |
| Migration | Table prefix mismatch | Ensure all package-owned migrations use `restotech_` physical names | Migration/test failure names exact table |
| Gate | No open Transaction Day | Reject POS mutation | Proposed domain error: `TRANSACTION_DAY_CLOSED` |
| Gate | No active Cashier Balance | Reject POS mutation | Proposed domain error: `CASHIER_BALANCE_REQUIRED` |
| Table Session | Dining Table inactive/missing | Reject open/session mutation | Proposed domain error: `DINING_TABLE_UNAVAILABLE` |
| Bill lock | Order mutation after bill generated | Reject mutation until bill unlock | Proposed domain error: `TABLE_SESSION_BILL_LOCKED` |
| Checkout | Bill not generated and quick-service disabled | Reject checkout | Proposed domain error: `BILL_REQUIRED_BEFORE_CHECKOUT` |
| Payment | Payment total mismatch | Roll back transaction | Proposed domain error: `PAYMENT_TOTAL_MISMATCH` |
| Employee Credit | Insufficient remaining credit | Roll back transaction | Proposed domain error: `EMPLOYEE_CREDIT_INSUFFICIENT` |
| Voucher | Missing/expired/used voucher | Roll back transaction | Proposed domain error: `VOUCHER_INVALID` |
| Stock | Insufficient stock and negative stock disabled | Roll back transaction | Proposed domain error: `STOCK_INSUFFICIENT` |
| Invoice number | Sequence lock/contention | Retry only if safe; otherwise fail transaction | Proposed domain error: `NUMBER_SEQUENCE_UNAVAILABLE` |
| Database | Any checkout write fails | Roll back entire checkout transaction | No partial invoice/session/stock mutation |

---

## VI. Security, Integrity, Idempotency, and Cleanup

- Use host Laravel authentication; do not introduce a package password-login identity table.
- Keep package authorization separate from host auth through Restotech profiles/roles/permissions.
- Do not trust POS client totals; server services must calculate totals from persisted menu/order/payment state.
- Use database transactions for checkout.
- Use row locks for number sequences, stock rows, voucher rows, employee credit rows, and active table session checkout as needed.
- Prevent duplicate checkout by locking the Table Session and rejecting already-paid/closed sessions.
- Store money as integer minor units and quantities as decimal precision.
- Keep immutable transaction rows immutable where practical; use correction workflows later rather than destructive edits.
- Do not log credentials, voucher codes unnecessarily, or sensitive host-user data.
- Publish config/assets without overwriting host files unless Laravel publishing flags explicitly request it.

---

## VII. Validation Strategy

### Chunk-aware validation commands

Use RTK-prefixed commands in this workflow.

- Changed-file discovery:
  - `rtk git status --short`
  - `rtk git diff --stat`
- Composer/package validation:
  - `rtk composer validate`
  - `rtk composer dump-autoload`
- PHP syntax checks where useful:
  - `rtk find src tests database config routes -name '*.php' -print`
  - `rtk php -l <changed-php-file>`
- Targeted tests:
  - `rtk vendor/bin/pest tests/Feature/PackageBootTest.php`
  - `rtk vendor/bin/pest tests/Feature/CheckoutFlowTest.php`
- Database integration:
  - `rtk docker compose -f docker-compose.test.yml up -d mariadb`
  - `rtk vendor/bin/pest --group=integration`
- Frontend shell validation when POS SPA begins:
  - `rtk npm run build`
  - `rtk npm run test` if tests exist
- Final review:
  - `rtk git diff --check`
  - `rtk git diff -- <changed-files>`

---

## VIII. Thin Vertical Slice Chunk Design

The implementation must proceed through `chunked-implementation`. Do not implement the full feature in one pass.

### Chunk 0: Discovery and Integration Confirmation

- **Goal:** Confirm target Laravel/Testbench versions, package rewrite strategy, and repository layout before editing.
- **Files to read:** `composer.json`, `README.md`, `docs/laravel-port/feature-scope.md`, `docs/laravel-port/model-mapping.md`, ADRs under `docs/adr/`.
- **Commands:** `rtk composer --version`, `rtk php -v`, targeted `rtk grep`/`rtk find` commands.
- **Evidence to confirm:** Laravel target major, PHP version, Testbench compatibility, whether old Yii files remain temporarily or are replaced.
- **Stop condition:** Written implementation ladder confirmed; no file edits.

### Chunk 1: Laravel Package Scaffold Stub

- **Goal:** Convert or create minimal Laravel package skeleton that autoloads and boots under Testbench.
- **Files to change:** `composer.json`, proposed `src/StandardServiceProvider.php`, proposed `config/restotech-standard.php`, proposed `tests/Pest.php`, proposed base test case.
- **Symbols to add/change:** Conceptual `Restotech\Standard\StandardServiceProvider`, package config array.
- **Implementation shape:** Add service provider with no-op boot/register beyond config merge; no routes/migrations yet.
- **Validation:** `rtk composer validate`, `rtk composer dump-autoload`, minimal Pest package boot test if dependencies installed.
- **Stop condition:** Composer autoload and provider class resolve.

### Chunk 2: Routes and Config Wiring Stubs

- **Goal:** Add configurable route groups with harmless placeholder endpoints.
- **Files to change:** provider/config plus proposed route files under `routes/`.
- **Symbols to add/change:** Conceptual route names/prefix config for Back Office, POS, Public API.
- **Implementation shape:** Register route files only when enabled; placeholder health/info endpoints return package/version metadata.
- **Validation:** Targeted route registration test through Testbench.
- **Stop condition:** Routes are configurable and boot without database.

### Chunk 3: Migration Harness and Core Identity/Settings Tables

- **Goal:** Prove migrations run under MariaDB with package prefix and host `users` left host-owned.
- **Files to change:** `database/migrations/*`, test database config, Docker compose test file.
- **Symbols to add/change:** Initial migrations for profiles, roles, permissions, settings, number sequences, transaction days, shifts, cashier balances.
- **Implementation shape:** Minimal columns only for first-slice gates and auth/profile linkage.
- **Validation:** `rtk docker compose -f docker-compose.test.yml up -d mariadb`, targeted migration test.
- **Stop condition:** Testbench migrates cleanly against MariaDB.

### Chunk 4: Dining/Menu/Inventory Model Skeletons

- **Goal:** Add minimal migrations/models needed to create a sellable menu item with recipe stock and a dining table.
- **Files to change:** migrations/models/factories or seeders for dining, menu, inventory.
- **Symbols to add/change:** DiningArea, DiningTable, MenuCategory, MenuItem, MenuUnit, MenuCondiment, MenuRecipeItem, InventoryCategory, InventoryItem, InventorySku, StorageLocation, StorageRack, StockBalance, StockMovement.
- **Implementation shape:** Eloquent models with relationships and minimal factories/seeders for tests; no full Back Office UI yet.
- **Validation:** Targeted model relationship/factory test.
- **Stop condition:** Test can seed a dining table, menu item, recipe, and stock balance.

### Chunk 5: Transaction Gates and Table Session Service Stub

- **Goal:** Implement transaction day/cashier gates and open table session behavior.
- **Files to change:** services/actions, models, POS route/controller tests.
- **Symbols to add/change:** Conceptual `OpenTableSession` action/service and gate checks.
- **Implementation shape:** Open session snapshots tax/service settings; reject closed day or missing cashier balance.
- **Validation:** Tests for closed-day rejection, missing-cashier rejection, successful session open.
- **Stop condition:** A table session can be opened through a service and tested without checkout.

### Chunk 6: Order Item and Bill Lock Slice

- **Goal:** Add order item lifecycle needed before checkout.
- **Files to change:** services/actions and tests for order items.
- **Symbols to add/change:** Conceptual order mutation services: add item, change quantity, add notes, void/free item, discounts, generate bill, unlock bill.
- **Implementation shape:** Keep server-calculated totals; enforce bill lock for later mutations.
- **Validation:** Tests for add item, discount, bill lock rejection, unlock success.
- **Stop condition:** A table session can reach bill-generated state with deterministic totals.

### Chunk 7: Checkout Core with Cash Payment

- **Goal:** Create sales invoice/items/payments and close table session atomically for ordinary payment.
- **Files to change:** checkout service/action, sales migrations/models, tests.
- **Symbols to add/change:** Conceptual `CheckoutTableSession` service.
- **Implementation shape:** Validate bill lock/payment totals; create invoice/items/payments; close paid session; no stock or special payments yet unless stubs safely reject.
- **Validation:** Happy-path cash checkout test and duplicate checkout rejection test.
- **Stop condition:** One table session produces one sales invoice with ordinary payment.

### Chunk 8: Special Payments and Stock Consumption

- **Goal:** Add Employee Credit, Voucher, optional Accounts Receivable payment recording, and recipe stock mutation.
- **Files to change:** checkout service, payment helpers, voucher/employee models, stock services, tests.
- **Symbols to add/change:** Conceptual payment validators and stock consumption service.
- **Implementation shape:** Lock employee/voucher/stock rows; debit credit; redeem voucher; record AR payment method; consume stock and write movements.
- **Validation:** Tests for insufficient employee credit, invalid voucher, insufficient stock, successful mixed-payment checkout.
- **Stop condition:** Checkout first-slice behavior matches Standard scope.

### Chunk 9: Minimal Back Office CRUD Foundation and First Screens

- **Goal:** Introduce internal CRUD foundation and enough screens to manage first-slice setup data.
- **Files to change:** Back Office controllers/components/views/tests.
- **Symbols to add/change:** Conceptual CRUD resource definitions for accepted first-slice Back Office list.
- **Implementation shape:** Start with one resource end-to-end, then add additional resources only after the foundation is validated.
- **Validation:** Feature tests for route auth, list/create/update for one representative resource, then targeted resource smoke tests.
- **Stop condition:** POS-operating setup can be managed through minimal Back Office screens.

### Chunk 10: POS Vue/Vite Shell and Internal API Wiring

- **Goal:** Add tablet POS shell that calls proven backend services without duplicating business logic.
- **Files to change:** frontend asset files, Vite config/package scripts if applicable, internal API controllers.
- **Symbols to add/change:** Conceptual POS routes/components for table selection/session/order/checkout.
- **Implementation shape:** Thin UI over services; browser/manual print only.
- **Validation:** `rtk npm run build`, targeted API/feature tests.
- **Stop condition:** POS shell can exercise the tested checkout flow in development.

---

## IX. Handoff to `chunked-implementation`

Recommended agent prompt:

```text
Use the chunked-implementation skill.
Use pre-read-discipline, safe-python-edit or the repository edit tool, and post-edit-discipline if available.
Use rtk-command-prefix for all shell commands.

Task:
Implement the Restotech Standard Laravel first-slice package scaffold and POS checkout path from docs/laravel-port/first-slice-implementation-spec.md.

Mode:
Execute Chunk 0 only. Do not edit files. Confirm repository evidence, Laravel/Testbench/PHP version assumptions, and the exact first implementation ladder. Stop after reporting findings.
```

After Chunk 0 is accepted:

```text
Use the chunked-implementation skill.
Use rtk-command-prefix for all shell commands.
Execute Chunk 1 only from docs/laravel-port/first-slice-implementation-spec.md.
Do not continue to Chunk 2.
After editing, run targeted validation and show git diff.
```

---

## X. Conclusion and Next Steps

The first-slice scope is stable enough to begin implementation planning and Chunk 0 discovery. Implementation must not start by building every model/controller at once. Start with package boot, then route/config stubs, then migrations/models/services/tests in thin vertical slices until checkout is proven.
