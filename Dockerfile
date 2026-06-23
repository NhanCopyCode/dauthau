FROM php:8.3-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo_mysql \
    gd \
    zip \
    mbstring \
    xml \
    bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files trước (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy toàn bộ code
COPY . .

# Generate key nếu chưa có, clear cache
RUN php artisan config:clear || true \
    && php artisan cache:clear || true

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}