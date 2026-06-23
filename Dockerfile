FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libzip-dev libxml2-dev libonig-dev \
    && docker-php-ext-install pdo_mysql gd zip mbstring xml bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    && chmod -R 775 storage bootstrap/cache

# Tạo start script
RUN echo '#!/bin/bash\n\
    php artisan migrate --force\n\
    php artisan config:cache\n\
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}' > /app/start.sh \
    && chmod +x /app/start.sh

EXPOSE 8080

CMD ["/app/start.sh"]