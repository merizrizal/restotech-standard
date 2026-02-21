# Operations and Setup Notes

## 1. What This Repository Provides

This repository provides module code, views, assets, and model/controller logic.
It does not provide a fully runnable standalone application out of the box.

Reasons:

- Depends on external package: `synctech/yii2-synctbase`.
- Uses host-injected variable `$mainModule` in config files.
- Uses host parameters and aliases not fully declared here.
- Includes no DB migration set in this repository.

## 2. Required Host Parameters

These keys are referenced in source and must exist in host app params (or equivalent merge):

- `checkbox-radio-script`
- `currencyOptions`
- `currencyOptionsPrint`
- `datepickerOptions`
- `errMysql`
- `navigation`
- `posModule`
- `subdomain`
- `subprogram`
- `timepickerOptions`
- `user.passwordResetTokenExpire`
- `version`

Some are defined locally (`datepickerOptions`, `timepickerOptions`, `errMysql`), others are expected from host config (`posModule`, `version`, `navigation`, currency options, script callbacks).

## 3. Required Host Aliases

Code references these aliases:

- `@rootUrl`
- `@uploads`
- `@uploadsUrl`
- `@restotech/standard/backend/media/css/report.css` (internal alias path use)

If `@rootUrl` / upload aliases are missing, login redirect and media rendering break.

## 4. Config File Notes

- Local config placeholders exist in each module config folder (`main-local.php`, `params-local.php`), but this extension config does not directly include them.
- `common/config/main.php` sets shared formatter defaults (timezone UTC, Indonesian-style separators).
- API config sets global JSON response format and JSON request parser.

## 5. Printing Integration

Printing uses raw socket calls:

- `backend/components/Tools.php::printToServer`
- Host DB settings required: `print_server_ip_address`, `print_server_port`

No explicit socket timeout/retry/circuit-breaker is implemented.

## 6. Testing State

There is a Codeception skeleton in `common/tests`, but it appears stale versus current namespace/model schema.

Examples:

- `common/tests/unit/models/LoginFormTest.php` imports `common\models\LoginForm`, while actual class is `restotech\standard\common\models\LoginForm`.
- Fixture shape in `common/tests/_data/user.php` uses fields from Yii advanced template user schema, not this project's `backend/models/User` schema.
- `common/codeception.yml` expects `config/test-local.php`, which is absent in repository.

Practical impact: tests are unlikely to run successfully without refactor.

## 7. Deployment/Runtime Checklist

Minimum checklist before deployment:

1. Ensure host app provides required params and aliases.
2. Ensure DB schema is provisioned externally (tables matching `backend/models/*`).
3. Validate print-server settings and socket reachability.
4. Verify access matrix (`user_level`, `user_akses`, `navigation`) is seeded.
5. Validate transaction-day settings (`transaction_day_*`) are populated.

