FROM php:8.5-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev nodejs npm \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/demo/entrypoint.sh /usr/local/bin/restotech-demo-entrypoint
RUN chmod +x /usr/local/bin/restotech-demo-entrypoint

WORKDIR /workspace
EXPOSE 8000
ENTRYPOINT ["restotech-demo-entrypoint"]
