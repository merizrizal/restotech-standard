#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${DEMO_APP_DIR:-/workspace/laravel-demo}"
PACKAGE_PATH="${PACKAGE_PATH:-/package}"
OVERLAY_PATH="${OVERLAY_PATH:-/overlay}"
DEMO_PORT="${DEMO_PORT:-8000}"

mkdir -p "${APP_DIR%/*}"

if [ ! -f "$APP_DIR/artisan" ]; then
    composer create-project laravel/laravel "$APP_DIR" '^13.0' --no-interaction --no-install --no-scripts --prefer-dist --no-progress
fi

cd "$APP_DIR"

cat > .env <<EOF
APP_NAME="Restotech Demo"
APP_ENV=${APP_ENV:-local}
APP_KEY=
APP_DEBUG=${APP_DEBUG:-true}
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:${DEMO_PORT}
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-mariadb}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-restotech_standard_demo}
DB_USERNAME=${DB_USERNAME:-restotech}
DB_PASSWORD=${DB_PASSWORD:-restotech}

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_STORE=file
QUEUE_CONNECTION=sync
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
MAIL_MAILER=log
EOF

cp -R "$OVERLAY_PATH"/. "$APP_DIR"/

php <<'PHP'
<?php
$files = ['artisan', 'public/index.php'];
foreach ($files as $file) {
    $contents = file_get_contents($file);
    if ($file === 'artisan') {
        $replacement = "require __DIR__.'/bootstrap/demo-autoload.php';\nrequire '/package/vendor/autoload.php';";
        $contents = str_replace(
            [
                "require __DIR__ . '/vendor/autoload.php';",
                "require __DIR__.'/vendor/autoload.php';",
            ],
            $replacement,
            $contents
        );
    } else {
        $replacement = "require __DIR__.'/../bootstrap/demo-autoload.php';\nrequire '/package/vendor/autoload.php';";
        $contents = str_replace(
            [
                "require __DIR__ . '/../vendor/autoload.php';",
                "require __DIR__.'/../vendor/autoload.php';",
            ],
            $replacement,
            $contents
        );
    }

    file_put_contents($file, $contents);
}
PHP

if [ -d "${PACKAGE_PATH}/node_modules" ]; then
    if [ -L node_modules ] || [ ! -e node_modules ]; then
        rm -f node_modules
        cp -R "${PACKAGE_PATH}/node_modules" node_modules
    fi
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --ansi >/dev/null
fi

php artisan migrate --seed --force --no-interaction
npm run build

echo "Restotech demo app is ready at http://localhost:${DEMO_PORT}"
exec php artisan serve --host=0.0.0.0 --port="${DEMO_PORT}"
