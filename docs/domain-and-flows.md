# Domain and Core Flows

## 1. Business Domain Areas

From models/controllers/views, this package covers these domains:

- Access and authorization
  - `User`, `UserLevel`, `UserAppModule`, `UserAkses`
- Menu and POS catalog
  - `Menu`, `MenuCategory`, `MenuCondiment`, `MenuRecipe`, `MenuHpp`, `MenuSatuan`
- Table and service session management
  - `Mtable`, `MtableCategory`, `MtableSession`, `MtableOrder`, table join/queue models
- Sales and payment
  - `SaleInvoice`, `SaleInvoiceTrx`, `SaleInvoicePayment`, AR payment/correction/retur models
- Inventory and stock movement
  - `Stock`, `StockMovement`, `Storage`, `StorageRack`, `StockKoreksi`
- Procurement
  - `DirectPurchase*`, `PurchaseOrder*`, `SupplierDelivery*`, `ReturPurchase*`
- Operational cash and financial report context
  - `TransactionCash`, `TransactionAccount`, `SaldoKasir`, `TransactionDay`
- Device/printing/configuration
  - `Printer`, `PaymentMethod`, `Settings`, socket print bridge in `Tools`

## 2. POS Sales Flow (Frontend)

Main flow path:

1. Open POS screen
   - `frontend/controllers/HomeController.php::actionLoadMenu`
2. Open/select table session
   - `frontend/controllers/HomeController.php::actionOpenTable`
3. Add/edit order lines
   - `frontend/controllers/ActionController.php`
   - actions: `save-order`, `change-qty`, `free-menu`, `void-menu`, `discount-*`, etc.
4. Move to payment
   - `frontend/controllers/HomeController.php::actionPayment`
5. Commit payment
   - `frontend/controllers/ActionController.php::actionPayment`
   - Creates invoice + invoice lines + payment rows
   - Updates stock and stock movement from menu recipes
6. Print bill/receipt
   - Uses print payload formatting in large POS views
   - Dispatch to print service via `Tools::printToServer`

API module mirrors this flow with JSON-returning controllers in `api/controllers/frontend/*`.

## 3. Stock Mutation Flow

### From Sales (outflow)

When payment is completed:

- Iterate sold menu lines
- Resolve menu recipes
- Reduce stock with `Stock::setStock(... negative)`
- Log movement as `Outflow-Menu`

Implemented in:

- `frontend/controllers/ActionController.php::actionPayment`
- `api/controllers/frontend/ActionController.php::actionPayment`

### From Direct Purchase (inflow)

When direct purchase is created/updated:

- Save detail lines (`DirectPurchaseTrx`)
- Increase stock via `Stock::setStock(... positive)`
- Create stock movement `Inflow-DP` or compensating outflow on deletion

Implemented in:

- `backend/controllers/DirectPurchaseController.php`

## 4. Transaction Day Flow

Transaction day acts as operational guardrail:

- Start day creates open transaction-day row.
- End day requires all tables closed.
- Frontend checks transaction-day state on each request and blocks/alerts when needed.

Implemented in:

- `backend/controllers/TransactionDayController.php`
- `frontend/controllers/TransactionDayController.php`
- `frontend/controllers/FrontendController.php` (status calculation)

## 5. Reporting Flow

Report generation style is mostly:

- Query + aggregate in controller
- Render partial HTML print template
- Export as PDF (Kartik mPDF) or Excel (raw response headers)

Main report controllers:

- `backend/controllers/SaleInvoiceController.php`
- `backend/controllers/SaleInvoicePaymentController.php`
- `backend/controllers/SaleInvoiceArPaymentController.php`
- `backend/controllers/PageController.php`

## 6. Initialization Flows

Some base data can be bootstrapped via one-click init actions:

- Payment methods: `backend/controllers/PaymentMethodController.php::actionInit`
- Room/table seed: `backend/controllers/MtableCategoryController.php::actionInit`
- Warehouse seed: `backend/controllers/StorageController.php::actionInit`

These use fixed IDs like `9999`, `9998`, `9997` for baseline records.

