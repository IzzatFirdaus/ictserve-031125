# Multi-stage build for ICTServe
# Stage 1: Node.js build environment
FROM node:22.14.0-alpine AS node-builder

# Set working directory for Node.js build
WORKDIR /app

# Create non-root user for Node.js operations
RUN addgroup -g 1001 nodeuser && \
    adduser -D -u 1001 -G nodeuser nodeuser

# Copy package files
COPY package*.json ./

# Configure npm for non-root user
RUN mkdir -p /tmp/.npm-cache /tmp/.npm-global && \
    npm config set cache /tmp/.npm-cache --global && \
    npm config set prefix /tmp/.npm-global --global && \
    chown -R nodeuser:nodeuser /app /tmp/.npm-cache /tmp/.npm-global

# Switch to non-root user for npm operations
USER nodeuser

# Install dependencies
RUN npm ci --prefer-offline --no-audit

# Copy source files (as nodeuser)
COPY --chown=nodeuser:nodeuser . .

# Build assets for production (will be copied to final stage if needed)
RUN npm run build

# Stage 2: PHP runtime environment
FROM php:8.2.12-fpm-alpine

# Keep things non-interactive
ARG COMPOSER_ALLOW_SUPERUSER=1

# Update CA certificates and repositories
RUN apk update --no-cache || apk add --no-cache ca-certificates && update-ca-certificates

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
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    shadow \
    sudo \
    $PHPIZE_DEPS

# Install Node.js 18.x (compatible with Vite 6.x)
# Use Alpine package manager for Node.js 18
RUN apk add --no-cache nodejs npm

# Verify Node.js installation
RUN node --version && npm --version

# Configure and install PHP extensions used by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-configure intl \
 && docker-php-ext-install -j$(nproc) pdo_mysql zip mbstring intl bcmath opcache gd pcntl

# Install redis php extension (phpredis) for cache/queue/session support
RUN pecl install redis \
 && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy custom PHP-FPM configuration
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/zzz-ictserve.conf

# Create application user and group matching www-data
RUN addgroup -g 82 -S www-data || true && \
    adduser -u 82 -D -S -G www-data www-data || true

# Create and set working directory
WORKDIR /var/www/html

# Build-time flag to control whether dev dependencies should be installed
ARG INSTALL_DEV=false

# Copy composer files and install dependencies early to leverage Docker cache
COPY composer.json composer.lock ./

# Install composer dependencies based on INSTALL_DEV flag
# This ensures consistent dependencies within the container environment
RUN if [ "$INSTALL_DEV" = "true" ]; then \
        echo "Installing composer dependencies with dev packages..." && \
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    else \
        echo "Installing composer dependencies without dev packages..." && \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    fi

# Copy application files
COPY . .

# Run composer scripts after copying application files
RUN composer run-script post-autoload-dump --no-interaction || true

# Ensure public/build directory exists for Vite
RUN mkdir -p public/build && chown -R www-data:www-data public/build

# Generate optimized autoload (dependencies are now installed)
RUN composer dump-autoload --optimize --no-scripts

# Configure npm for www-data user (for development mode)
RUN mkdir -p /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules && \
    chown -R www-data:www-data /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules && \
    chmod -R 775 /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules && \
    echo 'export NPM_CONFIG_CACHE=/var/www/.npm-cache' >> /etc/profile.d/npm.sh && \
    echo 'export NPM_CONFIG_PREFIX=/var/www/.npm-global' >> /etc/profile.d/npm.sh && \
    echo 'export PATH=/var/www/.npm-global/bin:$PATH' >> /etc/profile.d/npm.sh && \
    echo 'export NPM_CONFIG_FUND=false' >> /etc/profile.d/npm.sh && \
    echo 'export NPM_CONFIG_AUDIT=false' >> /etc/profile.d/npm.sh

# Ensure storage and cache directories are writable for the www-data user
RUN chown -R www-data:www-data storage bootstrap/cache public/build || true && \
    chmod -R 775 storage bootstrap/cache || true

EXPOSE 8000

# NOTE: For docker-based development this project provides a `.env.docker` file in the
# repository root. The `docker-compose.yml` references that file as `env_file: ./.env.docker`.
# When running containers without docker-compose you can still set environment variables
# or copy `.env.docker` to `.env` inside the container if necessary.

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/ictserve.ini

# Copy helper scripts and ensure they are executable
COPY scripts/docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh || true

# Create comprehensive entrypoint script with npm/Vite permission fixes
RUN echo '#!/bin/sh' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Wait for database' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '/usr/local/bin/wait-for-db.sh' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Fix Laravel permissions on startup' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Comprehensive npm and Vite permission fixes' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ -d /var/www/html/node_modules ]; then' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  echo "Fixing node_modules permissions..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  chown -R www-data:www-data /var/www/html/node_modules || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  chmod -R 775 /var/www/html/node_modules || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Ensure npm cache and global directories exist with correct permissions' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'mkdir -p /var/www/.npm-cache /var/www/.npm-global || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/.npm-cache /var/www/.npm-global || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/.npm-cache /var/www/.npm-global || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Create Vite temp directory proactively with correct permissions' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'mkdir -p /var/www/html/node_modules/.vite-temp || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/html/node_modules/.vite-temp || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/html/node_modules/.vite-temp || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Fix any existing build output permissions' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ -d /var/www/html/public/build ]; then' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  chown -R www-data:www-data /var/www/html/public/build || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '  chmod -R 775 /var/www/html/public/build || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Execute command' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec "$@"' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Entrypoint handles DB wait and permission fixes
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Default command for local development (bind mount will override code in container)
CMD ["php-fpm"]
