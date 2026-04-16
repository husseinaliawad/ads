FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.3-cli-bookworm
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring bcmath zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN chmod +x scripts/render-start.sh

EXPOSE 10000
CMD ["bash", "scripts/render-start.sh"]
