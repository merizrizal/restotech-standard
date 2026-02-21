# Insights and Risks

## Strengths

- Domain coverage is broad and coherent (POS, procurement, inventory, cash/reporting) in one extension package.
- Stock mutation is integrated into both procurement and sales paths.
- Transaction-day controls are enforced in both backend and frontend flows.
- Security-conscious packaging pattern (`deny from all` in module directories) indicates this is meant as internal module source, not direct web root.

## Key Risks and Findings

## High

1. API authentication is not implemented end-to-end.

- Empty login/logout actions: `api/controllers/SiteController.php:53`, `api/controllers/SiteController.php:57`
- API write actions still set user fields to `null` placeholders (token TODO):
  - `api/controllers/frontend/HomeController.php:81`
  - `api/controllers/frontend/ActionController.php:523`
  - `api/controllers/frontend/ActionController.php:538`

Impact:

- API is not ready for secure external/mobile exposure without auth/token identity integration.

2. Query composition uses string concatenation with request data in several report endpoints.

Examples:

- `backend/controllers/SaleInvoiceController.php:47`
- `backend/controllers/SaleInvoiceController.php:135`
- `backend/controllers/SaleInvoiceController.php:142`
- `backend/controllers/PageController.php:89`
- `backend/controllers/PageController.php:138`

Impact:

- Harder to reason about query safety and escaping.
- Increases risk surface if any validation assumptions are bypassed.

## Medium

3. Frontend POS and API POS logic are largely duplicated.

- `frontend/controllers/ActionController.php`
- `api/controllers/frontend/ActionController.php`
- `frontend/controllers/DataController.php`
- `api/controllers/frontend/DataController.php`
- `frontend/controllers/HomeController.php`
- `api/controllers/frontend/HomeController.php`

Impact:

- Higher maintenance cost and drift risk when changing business rules.

4. Transaction number generation is not atomic.

- `backend/models/Settings.php:82`
- increment/save sequence at `backend/models/Settings.php:110`

Impact:

- Under concurrency, duplicate/incorrect sequence behavior can occur without DB-level lock strategy.

5. Tests are outdated and likely non-runnable as-is.

- Namespace mismatch: `common/tests/unit/models/LoginFormTest.php:6`
- Fixture schema mismatch: `common/tests/_data/user.php:5`
- Missing referenced test config file: `common/codeception.yml:15`

Impact:

- Low confidence for safe refactor/change validation.

## Low

6. Probable stock badge condition bug in menu rendering.

- `frontend/views/data/_menu.php:17`
- `frontend/views/data/_condiment.php:19`

The expression `count($dataMenuRecipe['itemSku']['stocks'] > 0)` appears incorrect; expected shape is likely `count($dataMenuRecipe['itemSku']['stocks']) > 0`.

7. Unreachable duplicate return in direct purchase view action.

- `backend/controllers/DirectPurchaseController.php:73`

8. Typo-like CSS value in POS view.

- `frontend/views/home/_open_table.php:495` (`max-height: paperWidth0px`)

9. Socket print call has no timeout/retry/circuit-breaker.

- `backend/components/Tools.php:105`

## Recommended Priority Order

1. Implement API authentication/token identity and remove `null` user placeholders.
2. Parameterize report queries and centralize date-range filtering helpers.
3. Extract shared POS business service used by both frontend and API controllers.
4. Add integration tests for payment, stock mutation, and transaction-day gates.
5. Fix low-level correctness issues (stock badge condition, dead code, CSS typo).

