FROM php:8.2-fpm-alpine

# Keep things non-interactive
ARG COMPOSER_ALLOW_SUPERUSER=1

# System deps and build tools for extensions
RUN apk add --no-cache --update \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    zlib-dev \
    mysql-client \
    netcat-openbsd \
    nodejs \
    npm \
    $PHPIZE_DEPS

# Configure and install PHP extensions used by Laravel
RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j$(nproc) pdo_mysql zip mbstring intl bcmath opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create and set working directory
WORKDIR /var/www/html

# Build-time flag to control whether dev dependencies should be installed
ARG INSTALL_DEV=false

# Copy composer files and install dependencies early to leverage Docker cache
COPY composer.json composer.lock ./

# Install composer dependencies: with dev when INSTALL_DEV=true, otherwise without dev dependencies
RUN if [ "$INSTALL_DEV" = "true" ]; then \
            composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
        else \
            composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts || true; \
        fi

# Copy application
COPY . .

# Generate optimized autoload params (avoid running post-install scripts during build)
RUN composer dump-autoload --optimize --no-scripts || true

# Install npm dependencies and build assets (only if package.json exists)
RUN if [ -f package.json ]; then \
        npm ci --prefer-offline --no-audit && \
        npm run build; \
    fi

# Ensure storage and cache directories are writable for the www-data user
RUN chown -R www-data:www-data storage bootstrap/cache || true

EXPOSE 8000

# NOTE: For docker-based development this project provides a `.env.docker` file in the
# repository root. The `docker-compose.yml` references that file as `env_file: ./.env.docker`.
# When running containers without docker-compose you can still set environment variables
# or copy `.env.docker` to `.env` inside the container if necessary.

# Copy helper scripts and ensure they are executable
COPY scripts/docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh || true

# Entrypoint waits for DB availability before launching the app command
ENTRYPOINT ["/usr/local/bin/wait-for-db.sh"]

# Default command for local development (bind mount will override code in container)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
