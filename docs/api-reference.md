# API Reference (Current Implementation)

This section documents API controllers in `api/controllers/*` and `api/controllers/frontend/*`.

## 1. General Behavior

- Response format is JSON by default (`api/config/main.php`).
- JSON request parser is enabled for `application/json`.
- API user session is disabled (`enableSession => false`), but no token authentication is implemented in this repository.

## 2. `SiteController`

File: `api/controllers/SiteController.php`

Actions:

- `POST /site/login`
  - Method exists but currently empty implementation.
- `POST /site/logout`
  - Method exists but currently empty implementation.
- `/site/print`
  - Forwards payload to socket print server (`Tools::printToServer`).
  - Reads body or query params.
  - Not explicitly verb-restricted in behaviors.
- `POST /site/get-datetime`
  - Returns `{date, time}` in Asia/Jakarta formatter context.

## 3. `frontend/HomeController`

File: `api/controllers/frontend/HomeController.php`

Verb-restricted (POST):

- `/frontend/home/transaction`
- `/frontend/home/open-table`
- `/frontend/home/payment`
- `/frontend/home/reprint-invoice`
- `/frontend/home/reprint-invoice-submit`

Behavior summary:

- `transaction` opens fallback table/session `9999`.
- `open-table` creates/reuses active table session and returns session/order structure.
- `payment` returns session + available payment methods.
- `reprint-invoice-submit` loads invoice with payment relations by posted invoice id.

## 4. `frontend/DataController`

File: `api/controllers/frontend/DataController.php`

Verb-restricted (POST):

- `/frontend/data/search-menu`
- `/frontend/data/menu-category`
- `/frontend/data/menu`

Other actions present (no explicit verb filter entry):

- `/frontend/data/datetime`
- `/frontend/data/condiment`

Behavior summary:

- Menu/category browsing and search.
- Includes deep joins for recipes, stock, category printers.

## 5. `frontend/ActionController`

File: `api/controllers/frontend/ActionController.php`

Verb-restricted (POST):

- `/frontend/action/save-order`
- `/frontend/action/info-tamu`
- `/frontend/action/catatan`
- `/frontend/action/free-menu`
- `/frontend/action/void-menu`
- `/frontend/action/discount-bill`
- `/frontend/action/discount-menu`
- `/frontend/action/close-table`
- `/frontend/action/print-bill`
- `/frontend/action/unlock-bill`
- `/frontend/action/change-qty`
- `/frontend/action/payment`

Behavior summary:

- Mirrors frontend POS actions but returns JSON arrays.
- Payment action performs full write path:
  - close session(s)
  - create invoice
  - create invoice lines
  - adjust stock
  - create stock movement
  - create payment rows

## 6. Practical Notes

- API code currently uses `null` placeholders where user identity from token should be used (e.g., `user_opened`, `user_closed`, `user_operator`).
- `login/logout` are stubs, so API authentication must be added before production exposure.
- Route prefix resolution still depends on external `$mainModule` and host routing setup.

