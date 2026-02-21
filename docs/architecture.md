# Architecture

## 1. Packaging Model

This repository is distributed as a Yii2 extension (`type: yii2-extension`) and exposes a root module class:

- `Module.php`: registers submodules `backend`, `frontend`, `api`.
- Namespace root: `restotech\standard\`.
- Composer dependency: `synctech/yii2-synctbase` (critical base framework dependency).

The module classes are thin:

- `backend/BackendModule.php`
- `frontend/FrontendModule.php`
- `api/ApiModule.php`

They mostly rely on inherited framework behavior.

## 2. Submodule Design

### Backend

Core back-office module for:

- Master data
- Configuration
- Reports (sales, cash, receivable)
- Stock/purchase operations

Base controller:

- `backend/controllers/BackendController.php`

Key behavior:

- Extends `\synctech\SynctBaseController`.
- Sets dynamic view path to `@restotech/standard/backend/views/<controller-id>`.
- Loads and caches company profile settings in session (`company_settings_profile`).

### Frontend

POS UI module (AJAX-heavy, long inline JS views).

Base controller:

- `frontend/controllers/FrontendController.php`

Key behavior:

- Extends `\synctech\SynctBaseController`.
- Performs transaction-day checks before most actions.
- Injects transaction-day status into layout params.

### API

REST-style JSON module:

- `api/controllers/SiteController.php`
- `api/controllers/frontend/HomeController.php`
- `api/controllers/frontend/DataController.php`
- `api/controllers/frontend/ActionController.php`

Most API controllers extend `\yii\rest\Controller` and return array payloads.

## 3. Config Layering and Runtime Assumptions

Config files are extension-level defaults and merge with common config.

- `backend/config/main.php`
- `frontend/config/main.php`
- `api/config/main.php`
- `common/config/main.php`

Important detail:

- All `* /config/main.php` files use `$mainModule` (for default route/login/error route prefixes), but `$mainModule` is not defined in this repository.
- Example: `backend/config/main.php:12`, `frontend/config/main.php:12`, `api/config/main.php:12`.

This confirms the package expects host-application bootstrap/config injection.

## 4. Host-App Dependencies (Not Fully Defined Here)

Used params outside extension-local defaults:

- `posModule`
- `version`
- `navigation`
- `currencyOptions`
- `currencyOptionsPrint`
- `checkbox-radio-script`
- `subprogram`, `subdomain`

Used aliases outside extension-local definition:

- `@rootUrl`, `@uploads`, `@uploadsUrl`

Without these, several routes, print formatting, UI behavior, and media URLs fail.

## 5. Auth and Access Pattern

- User identity class: `restotech\standard\backend\models\User`.
- Login logic: `common/models/LoginForm.php`.
- Access gates are inherited via `getAccess()` from SynctBase controllers.
- User session stores `user_data` with:
  - employee profile
  - role level
  - default action route
  - module access list (`userAkses`)

## 6. UI Composition

### Backend UI

- AdminLTE-based layout (`backend/assets/AdminlteAssets.php`, `backend/views/layouts/main.php`).
- Sidebar and header generated via widgets in `backend/components/*`.

### Frontend POS UI

- Main layout: `frontend/views/layouts/main.php`.
- Home screen is loaded dynamically with AJAX fragments (`frontend/views/home/*`).
- Very large view files include substantial business logic in JavaScript.

## 7. Extension Security Posture (Structural)

- `.htaccess` in `backend/`, `frontend/`, `api/` says `deny from all`.
- This is consistent with source-as-module packaging, not direct web-root deployment.

