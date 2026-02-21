# Data Model Overview

`backend/models` contains `57` ActiveRecord classes mapped to database tables.
All of them extend `\synctech\SynctBaseModel`.

For the full generated class/table list, see `docs/appendix-model-index.md`.

## 1. Identity and Access

Core tables:

- `user`
- `user_level`
- `user_app_module`
- `user_akses`
- `employee`

Role model:

- `user_level` defines default action and role metadata.
- `user_akses` maps role to allowed app modules.
- Login stores resolved role and access matrix into session (`common/models/LoginForm.php`).

## 2. POS Transaction Core

Core tables:

- `mtable`, `mtable_category`
- `mtable_session`, `mtable_order`, `mtable_order_queue`
- `mtable_join`, `mtable_session_join`, `mtable_booking`

Modeling choices:

- Active dining activity is session-centric (`mtable_session`).
- Ordered menu hierarchy supports parent-child order rows (main + condiment).
- Session includes billing flags (`bill_printed`, `is_paid`, close/open timestamps).

## 3. Menu and Recipe

Core tables:

- `menu`, `menu_category`, `menu_category_printer`
- `menu_recipe`, `menu_condiment`, `menu_hpp`, `menu_satuan`

Notes:

- Printer routing can be attached by menu category.
- Recipe rows tie menu sale to inventory consumption.
- HPP history supports report-time cost lookup.

## 4. Sales and Payment

Core tables:

- `sale_invoice`
- `sale_invoice_trx`
- `sale_invoice_payment`
- related correction/retur/AR tables:
  - `sale_invoice_correction`
  - `sale_invoice_trx_correction`
  - `sale_invoice_payment_correction`
  - `sale_invoice_retur`
  - `sale_invoice_ar_payment`

Notes:

- Sales are posted from table session closure/payment flow.
- Payment supports multiple methods and additional context fields (`keterangan`, code mapping logic).

## 5. Stock and Storage

Core tables:

- `stock`
- `stock_movement`
- `stock_koreksi`
- `storage`, `storage_rack`
- item master:
  - `item`, `item_sku`, `item_category`

Notes:

- Stock key composition in code is `item + sku + storage + rack` (`Stock::setStock`).
- Movement logs include inflow and outflow helper methods (`StockMovement::setInflow`, `setOutflow`).

## 6. Procurement and Supplier Chain

Core tables:

- `direct_purchase`, `direct_purchase_trx`
- `purchase_order`, `purchase_order_trx`
- `supplier`, `supplier_delivery`, `supplier_delivery_trx`
- `supplier_delivery_invoice`, `supplier_delivery_invoice_trx`, `supplier_delivery_invoice_payment`
- `retur_purchase`, `retur_purchase_trx`

Notes:

- Direct purchase has implemented stock mutation flow.
- Other supplier/PO controllers in this repository are mostly shell wrappers around base behavior.

## 7. Finance, Operations, and Config

Core tables:

- `transaction_cash`, `transaction_account`
- `saldo_kasir`, `shift`, `transaction_day`
- `voucher`, `payment_method`, `printer`
- `settings`

Notes:

- `settings` is a high-impact table used for:
  - transaction number generation
  - tax/service settings
  - print templates/print-server endpoints
  - company profile/session bootstrap

## 8. Transaction Numbering Strategy

ID/number generation is centralized in `Settings::getTransNumber()`:

- Reads `<key>` and `<key>_format` rows.
- Builds number from format tokens (`{date}`, `{inc}`, optional `{AA}`).
- Increments `setting_value` after generation.

Used by menu/item/employee/category/invoice/direct purchase creation flows.

