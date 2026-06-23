FROM php:8.3-cli

# System dependencies + Node.js 20
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libzip-dev libxml2-dev libonig-dev \
    && docker-php-ext-install pdo_mysql gd zip mbstring xml bcmath \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Install Node dependencies + build Vite assets
COPY package.json package-lock.json ./
RUN npm ci

# Copy toàn bộ code
COPY . .

# Build frontend
RUN npm run build

# Storage permissions
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    && chmod -R 775 storage bootstrap/cache

# Start script
RUN printf '#!/bin/bash\nset -e\nphp artisan migrate --force\nphp artisan config:cache\nphp artisan serve --host=0.0.0.0 --port=${PORT:-8080}\n' > /app/start.sh \
    && chmod +x /app/start.sh

EXPOSE 8080

CMD ["/app/start.sh"]