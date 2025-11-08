# ---------- STAGE 1: composer ----------
FROM php:8.3-fpm-alpine AS composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    COMPOSER_HOME=/tmp/composer

RUN apk add --no-cache \
      git unzip ca-certificates openssl \
      icu-dev libzip-dev oniguruma-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev \
      libxml2-dev \
 && update-ca-certificates

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" mbstring intl zip pdo_mysql bcmath gd exif dom fileinfo

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY composer.json composer.lock ./

RUN set -eux; \
    test -f composer.lock; \
    php -v; \
    php -m | sort; \
    composer --version; \
    composer validate --no-check-publish --no-check-lock -v || echo ">> composer validate FAILED (non-blocking)"; \
    composer diagnose -vvv || echo ">> composer diagnose FAILED (non-blocking)"; \
    composer check-platform-reqs -vvv || true

ENV COMPOSER_PROCESS_TIMEOUT=2000

RUN set -euo pipefail; \
    { composer install \
        --no-dev --prefer-dist --no-interaction --no-progress --no-scripts \
        --no-autoloader \
        -vvv --profile 2>&1 | tee /tmp/composer-install.log; } \
    || { echo "===== COMPOSER INSTALL FAILED (últimas 200 líneas) ====="; \
         tail -n 200 /tmp/composer-install.log || true; \
         exit 1; }

# copiamos el proyecto
COPY . .
RUN echo "===== RUTAS QUE RECIBE DOCKER =====" \
 && sed -n '1,120p' routes/web.php \
 && echo "===== FIN RUTAS ====="

# 1) creamos las carpetas para que el classmap no llore
# 2) dump-autoload SIN scripts
RUN mkdir -p database/seeders database/factories \
 && composer dump-autoload -o --no-scripts


# ---------- STAGE 2: node / MIX ----------
FROM node:20-alpine AS nodebuild
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit --no-fund || npm install --no-audit --no-fund
COPY . .
RUN npm run production

# ---------- STAGE 3: runtime ----------
FROM php:8.3-fpm-alpine AS runtime

# libs de runtime (quedan en la imagen)
RUN apk add --no-cache \
      nginx supervisor curl bash tzdata ca-certificates \
      icu libzip oniguruma libpng libjpeg-turbo freetype libxml2 \
 && update-ca-certificates

# deps de compilación (se borran después)
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS \
      icu-dev libzip-dev oniguruma-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" opcache intl zip bcmath pdo_mysql gd exif dom fileinfo \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .build-deps

# tunning PHP
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.jit=1255"; \
      echo "opcache.jit_buffer_size=64M"; \
      echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini \
 && { \
      echo "memory_limit=512M"; \
      echo "upload_max_filesize=50M"; \
      echo "post_max_size=50M"; \
      echo "expose_php=0"; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# vendor + código desde la stage composer
COPY --from=composer  /app /var/www/html

# Assets compilados
COPY --from=nodebuild /app/public/js                /var/www/html/public/js
COPY --from=nodebuild /app/public/css               /var/www/html/public/css
COPY --from=nodebuild /app/public/mix-manifest.json /var/www/html/public/

# Usuario no root (si los dirs no existen aún, no rompas el build)
RUN adduser -D -H -u 1000 www \
 && mkdir -p storage bootstrap/cache \
 && chown -R www:www storage bootstrap/cache

# Nginx + Supervisor
RUN mkdir -p /run/nginx
COPY .docker/nginx.conf       /etc/nginx/nginx.conf
COPY .docker/site.conf        /etc/nginx/http.d/default.conf
COPY .docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8000
ENV APP_ENV=production
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]