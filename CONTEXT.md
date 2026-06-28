# Restotech Standard

Restotech Standard is a restaurant point-of-sale and back-office domain package. It uses English domain language for Laravel package code, database schema, APIs, tests, and documentation.

## Language

**Standard Edition**:
The base Restotech product scope containing the core restaurant point-of-sale and back-office capabilities.
_Avoid_: Standard version when discussing package boundaries

**Full Edition**:
An extended Restotech product scope that adds richer operational capabilities beyond the Standard Edition, such as advanced table or billing workflows.
_Avoid_: Full version when discussing package boundaries

**Point of Sale**:
The tablet-friendly operator surface used to manage dining tables, table sessions, orders, billing, and payments during restaurant service.
_Avoid_: Frontend, cashier screen when referring to the full operator surface

**Back Office**:
The administrative surface used to manage restaurant master data, access, configuration, procurement, inventory, cash operations, and reports.
_Avoid_: Backend when referring to the business surface

**Dining Table**:
A physical or service-location table where guests are seated and served during a restaurant visit.
_Avoid_: Mtable, table master

**Table Session**:
A service period for one or more dining tables, from opening the table through ordering, billing, payment, and closure.
_Avoid_: Mtable session

**Order Item**:
A menu item or condiment requested during a table session before it is posted to a sales invoice.
_Avoid_: Order trx, transaction row

**Sales Invoice**:
The posted sale created when a table session is billed and paid.
_Avoid_: Sale invoice, bill

**Sales Invoice Item**:
A line item on a sales invoice representing a sold menu item, condiment, discount, or related sale detail.
_Avoid_: Sale invoice trx

**Transaction Number**:
A human-readable business identifier assigned to operational records such as invoices, purchases, stock movements, and other transactions.
_Avoid_: Settings counter, raw database ID

**Money**:
A monetary amount used for prices, payments, discounts, taxes, service charges, and totals.
_Avoid_: Float amount

**Quantity**:
The count or amount of items in an order, purchase, stock movement, or invoice line.
_Avoid_: Jumlah item

**Unit Price**:
The price for one unit of an item before multiplying by quantity.
_Avoid_: Harga satuan

**Notes**:
Free-form explanatory text attached to a record.
_Avoid_: Keterangan

**Cashier Balance**:
The cash position assigned to a cashier shift or operating period.
_Avoid_: Saldo kasir

**Employee Credit**:
A payment method that charges a sale against an employee's allowed credit balance.
_Avoid_: Limit officer, XLIMIT

**Voucher**:
A payment instrument identified by a code that can be applied to reduce or pay part of a sales invoice according to its value and validity rules.
_Avoid_: XVCHR

**Purchase Return**:
A return of purchased goods to a supplier.
_Avoid_: Retur purchase
