# Laravel Port Model Mapping

This document maps Yii/SQL model names from Restotech Standard and Restotech Full to proposed Laravel English domain names. It is an initial planning map, not a migration specification.

## Evidence Sources

- Standard model inventory: `docs/appendix-model-index.md`.
- Standard schema reference: `docs/restotech.sql`.
- Standard domain notes: `docs/data-model.md` and `docs/domain-and-flows.md`.
- Feature boundary: `docs/laravel-port/feature-scope.md`.
- Full repo model references: `/home/meriz/Documents/Synctech.ID/PHP/restotech-full/docs/appendix-model-index.md`.
- Full repo flows: `/home/meriz/Documents/Synctech.ID/PHP/restotech-full/docs/domain-and-flows.md`.

## Ownership Labels

- **Standard**: belongs in `restotech/standard` Laravel.
- **Standard support**: table/model is needed by Standard behavior, but not necessarily exposed as a full first-slice CRUD surface.
- **Full candidate**: behavior is implemented in Yii `restotech-full`; do not build into Laravel Standard unless reclassified.
- **Deferred**: may be Standard eventually, but not first slice.
- **Needs mapping**: inspect source again before coding.
- **Host/Replaced**: Laravel host app or new Laravel design replaces the Yii table directly.

## Naming Rules

- Use English names in Laravel code, schema, APIs, tests, and docs.
- Use Laravel-style bigint primary keys.
- Keep human-readable business identifiers as separate unique columns such as `invoice_number`, `purchase_number`, or `code`.
- Use `created_by`, `updated_by`, and where applicable `deleted_by` for host-user audit fields.
- Use soft deletes for master/config records, not immutable transaction rows.
- Money columns use integer minor units; quantity columns use decimal precision.
- Package-owned tables use a `restotech_` physical prefix to avoid host-app table collisions.
- The table names below are logical Laravel names; physical migration names should prefix package-owned tables, for example `dining_tables` becomes `restotech_dining_tables`.

## Core Identity and Authorization

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `User` | `user` | Host `User` + `RestotechUserProfile` | host `users` + `restotech_user_profiles` | Host/Replaced | Host Laravel auth is authoritative. Package stores Restotech profile/linkage only. |
| `Employee` | `employee` | `Employee` | `employees` | Standard | Keep employee profile and employee-credit fields; use host user link instead of Yii password login. |
| `UserLevel` | `user_level` | `Role` | `roles` | Standard | Restotech authorization role, not host auth identity. |
| `UserAppModule` | `user_app_module` | `Permission` or `AccessModule` | `permissions` / `access_modules` | Standard | Needs final permission vocabulary during authorization design. |
| `UserAkses` | `user_akses` | `RolePermission` | `role_permissions` | Standard | Role-to-permission mapping. |

## POS Tables and Sessions

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `MtableCategory` | `mtable_category` | `DiningArea` | `dining_areas` | Standard support | Old labels are room/category. Needed to organize dining tables. Full has richer layout/admin. |
| `Mtable` | `mtable` | `DiningTable` | `dining_tables` | Standard | Include capacity, inactive flag, tax/service exemptions, image/layout fields if needed. |
| `MtableSession` | `mtable_session` | `TableSession` | `table_sessions` | Standard | Core Standard POS session aggregate. Snapshot tax/service charge at open. |
| `MtableOrder` | `mtable_order` | `OrderItem` | `order_items` | Standard | Belongs to a table session before invoice posting. Supports notes, discounts, void/free flags. |
| `MtableOrderQueue` | `mtable_order_queue` | `KitchenQueueItem` | `kitchen_queue_items` | Full candidate | Yii Full implements queue actions and views. Standard should expose extension point only. |
| `MtableJoin` | `mtable_join` | `TableJoin` | `table_joins` | Full candidate | Yii Full join-table workflow. |
| `MtableSessionJoin` | `mtable_session_join` | `TableJoinSession` | `table_join_sessions` | Full candidate | Join association for Full Edition. |
| `MtableBooking` | `mtable_booking` | `TableBooking` | `table_bookings` | Full candidate | Yii Full implements booking list/create/open workflow. Standard may keep extension readiness only. |

## Menu, Catalog, and Recipes

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `MenuCategory` | `menu_category` | `MenuCategory` | `menu_categories` | Standard | Include inactive, discount-disabled, and queue-related flags; queue behavior is Full candidate. |
| `Menu` | `menu` | `MenuItem` | `menu_items` | Standard | Sellable POS item. Keep cost/price/image fields. |
| `MenuSatuan` | `menu_satuan` | `MenuUnit` | `menu_units` | Standard | Unit of measure for menu items. |
| `MenuRecipe` | `menu_recipe` | `MenuRecipeItem` | `menu_recipe_items` | Standard | Defines inventory consumption during checkout. |
| `MenuCondiment` | `menu_condiment` | `MenuCondiment` | `menu_condiments` | Standard | Condiment/options relationship. |
| `MenuHpp` | `menu_hpp` | `MenuCostHistory` | `menu_cost_histories` | Needs mapping | Used for historical cost/reporting; confirm Standard requirements before first slice. |
| `MenuCategoryPrinter` | `menu_category_printer` | `MenuCategoryPrinter` | `menu_category_printers` | Deferred | Printer routing data; kitchen printer automation is deferred/Full-adjacent. |
| `Printer` | `printer` | `Printer` | `printers` | Deferred | Customer receipt uses abstraction first; physical/network printer driver later. |

## Inventory Master and Stock

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `ItemCategory` | `item_category` | `InventoryCategory` | `inventory_categories` | Standard support | Needed for recipe stock items. |
| `Item` | `item` | `InventoryItem` | `inventory_items` | Standard support | Ingredient/stock item, distinct from sellable menu item. |
| `ItemSku` | `item_sku` | `InventorySku` | `inventory_skus` | Standard support | Stock is tracked at SKU + storage + optional rack. |
| `Storage` | `storage` | `StorageLocation` | `storage_locations` | Standard support | Needed for stock tracking; Full has richer CRUD. |
| `StorageRack` | `storage_rack` | `StorageRack` | `storage_racks` | Standard support | Optional sub-location. |
| `Stock` | `stock` | `StockBalance` | `stock_balances` | Standard | Checkout and direct purchase mutate stock. Use uniqueness across SKU/storage/rack. |
| `StockMovement` | `stock_movement` | `StockMovement` | `stock_movements` | Standard support | Standard checkout writes `Outflow-Menu`; Full adds manual/transfer/conversion/correction views. |
| `StockKoreksi` | `stock_koreksi` | `StockAdjustment` | `stock_adjustments` | Full candidate | Yii Full implements stock correction/opname verification. |

## Sales, Payments, and Corrections

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `SaleInvoice` | `sale_invoice` | `SalesInvoice` | `sales_invoices` | Standard | Posted sale from table checkout. Store snapshot totals and rates. |
| `SaleInvoiceTrx` | `sale_invoice_trx` | `SalesInvoiceItem` | `sales_invoice_items` | Standard | Posted invoice line item. |
| `SaleInvoicePayment` | `sale_invoice_payment` | `SalesInvoicePayment` | `sales_invoice_payments` | Standard | Multiple payments per invoice. Supports employee credit/voucher context. |
| `SaleInvoiceArPayment` | `sale_invoice_ar_payment` | `ReceivablePayment` | `receivable_payments` | Deferred | Accounts receivable is eventual parity; exact ownership needs mapping. |
| `SaleInvoiceCorrection` | `sale_invoice_correction` | `SalesInvoiceCorrection` | `sales_invoice_corrections` | Full candidate | Yii Full implements payment/invoice correction. |
| `SaleInvoiceTrxCorrection` | `sale_invoice_trx_correction` | `SalesInvoiceItemCorrection` | `sales_invoice_item_corrections` | Full candidate | Correction detail snapshot. |
| `SaleInvoicePaymentCorrection` | `sale_invoice_payment_correction` | `SalesInvoicePaymentCorrection` | `sales_invoice_payment_corrections` | Full candidate | Correction payment snapshot. |
| `SaleInvoiceRetur` | `sale_invoice_retur` | `SalesReturnItem` | `sales_return_items` | Full candidate | Yii Full exposes sale invoice refund/correction screens. |
| `PaymentMethod` | `payment_method` | `PaymentMethod` | `payment_methods` | Standard | Required for checkout and payables later. |
| `Voucher` | `voucher` | `Voucher` | `vouchers` | Standard support / Full admin | Standard owns voucher payment validation and redemption during checkout; Yii Full owns voucher Back Office CRUD and issuance/management screens. |

## Operations and Finance

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `Shift` | `shift` | `Shift` | `shifts` | Standard | Needed for cashier sessions. |
| `SaldoKasir` | `saldo_kasir` | `CashierBalance` | `cashier_balances` | Standard | Active cashier balance/session required for POS mutations. |
| `TransactionDay` | `transaction_day` | `TransactionDay` | `transaction_days` | Standard | Restaurant-wide open/close business day gate. |
| `TransactionAccount` | `transaction_account` | `CashAccount` | `cash_accounts` | Full candidate | Yii Full implements cash-in/out account CRUD. |
| `TransactionCash` | `transaction_cash` | `CashTransaction` | `cash_transactions` | Full candidate | Yii Full implements cash-in/out flows and finance activity report. |
| `Settings` | `settings` | `Setting` + `NumberSequence` | `settings`, `number_sequences` | Standard | Split generic settings from transaction number generation. Use locked sequences. |

## Procurement and Suppliers

| Yii model | Yii table | Laravel model | Logical Laravel table | Ownership | Notes |
|---|---|---|---|---|---|
| `DirectPurchase` | `direct_purchase` | `DirectPurchase` | `direct_purchases` | Standard | Standard controller implements create/update/delete/print and stock mutation. |
| `DirectPurchaseTrx` | `direct_purchase_trx` | `DirectPurchaseItem` | `direct_purchase_items` | Standard | Detail lines for direct purchase. |
| `Supplier` | `supplier` | `Supplier` | `suppliers` | Full candidate | Yii Full implements supplier CRUD; Standard has model dependency. Reclassify if direct purchase requires supplier admin. |
| `PurchaseOrder` | `purchase_order` | `PurchaseOrder` | `purchase_orders` | Full candidate | Standard controller is shell; Yii Full implements PO workflow. |
| `PurchaseOrderTrx` | `purchase_order_trx` | `PurchaseOrderItem` | `purchase_order_items` | Full candidate | PO detail rows. |
| `SupplierDelivery` | `supplier_delivery` | `SupplierDelivery` | `supplier_deliveries` | Full candidate | Yii Full implements receiving and stock inflow. |
| `SupplierDeliveryTrx` | `supplier_delivery_trx` | `SupplierDeliveryItem` | `supplier_delivery_items` | Full candidate | Receiving detail rows. |
| `SupplierDeliveryInvoice` | `supplier_delivery_invoice` | `SupplierInvoice` | `supplier_invoices` | Full candidate | Payable invoice. |
| `SupplierDeliveryInvoiceTrx` | `supplier_delivery_invoice_trx` | `SupplierInvoiceItem` | `supplier_invoice_items` | Full candidate | Payable invoice detail. |
| `SupplierDeliveryInvoicePayment` | `supplier_delivery_invoice_payment` | `SupplierInvoicePayment` | `supplier_invoice_payments` | Full candidate | Payable payment. |
| `ReturPurchase` | `retur_purchase` | `PurchaseReturn` | `purchase_returns` | Full candidate | Yii Full implements return workflow. |
| `ReturPurchaseTrx` | `retur_purchase_trx` | `PurchaseReturnItem` | `purchase_return_items` | Full candidate | Purchase return detail rows. |

## Common Column Translation

| Legacy column | Laravel name | Notes |
|---|---|---|
| `nama_*` | `name` / specific `*_name` | Prefer context-specific English when needed. |
| `keterangan`, `catatan` | `notes` | Use `notes` for free-form explanatory text. |
| `jumlah`, `jumlah_item`, `jumlah_order`, `jumlah_terima` | `quantity`, `item_quantity`, `ordered_quantity`, `received_quantity` | Use decimal precision. |
| `jumlah_harga` | `total_amount` | Integer money. |
| `harga_satuan` | `unit_price` | Integer money. |
| `harga_jual` | `sale_price` | Integer money. |
| `harga_pokok` | `cost_amount` | Integer money. |
| `pajak` | `tax_rate` / `tax_amount` | Store both rate and calculated amount where applicable. |
| `service_charge` | `service_charge_rate` / `service_charge_amount` | Store both rate and calculated amount where applicable. |
| `not_active` | `is_active` or `deactivated_at` | Prefer positive boolean or timestamp. |
| `is_deleted` | `deleted_at` | Use Laravel soft deletes for master records. |
| `user_created`, `user_updated` | `created_by`, `updated_by` | Reference host Laravel users. |
| `kd_karyawan` | `employee_code` | Legacy employee identifier. |
| `kd_supplier` | `supplier_code` | Legacy supplier identifier. |
| `nama_tamu`, `nama_pelanggan` | `guest_name` | Booking customer name maps to guest name when opening a session. |
| `nama_meja` | `table_name` | Dining table display name. |
| `nama_category` | `name` | Category/area name by table context. |
| `nama_menu` | `name` | Menu item display name. |
| `nama_sku` | `sku_name` | Inventory SKU display name. |
| `stok_minimal` | `minimum_stock_quantity` | Decimal quantity. |
| `jumlah_stok` | `on_hand_quantity` | Decimal quantity. |
| `tanggal` | `occurred_at` | For stock movements. |
| `date` | `business_date` or `date` | Prefer `business_date` for transaction-day/reporting context. |
| `start`, `end` | `started_at`, `ended_at` | For transaction day. |
| `sisa` | `remaining_credit_amount` | Employee Credit balance. |
| `limit_officer` | `credit_limit_amount` | Employee Credit limit. |

## First-Slice Model Set

For the first Standard POS checkout slice, implement only the minimal model set needed to prove the flow:

- `Employee`
- `RestotechUserProfile`
- `Role`, `Permission`, `RolePermission`
- `DiningArea`, `DiningTable`
- `TableSession`, `OrderItem`
- `MenuCategory`, `MenuItem`, `MenuUnit`, `MenuCondiment`, `MenuRecipeItem`
- `InventoryCategory`, `InventoryItem`, `InventorySku`, `StorageLocation`, `StorageRack`
- `StockBalance`, `StockMovement`
- `PaymentMethod`, `Voucher`
- `SalesInvoice`, `SalesInvoiceItem`, `SalesInvoicePayment`
- `Shift`, `CashierBalance`, `TransactionDay`
- `Setting`, `NumberSequence`

## Open Questions

1. Is Employee Credit validation/admin Standard or Full?
2. Which Back Office CRUD screens are required for operating Standard POS versus provided only by Full?
3. Does Direct Purchase remain Standard in Laravel, or should it become Full because richer procurement lives in Full?
4. Should `MenuCategoryPrinter` and `Printer` remain Standard deferred features or move to a Full/kitchen-printing extension?
