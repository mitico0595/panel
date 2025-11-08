# =========================================================
# STAGE 1: BUILD (Composer + Vite)
# =========================================================
FROM php:8.3-fpm-alpine AS build

# Paquetes del sistema + Node
RUN apk add --no-cache \
    git unzip curl bash \
    icu-dev libzip-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev \
    nodejs npm

# Extensiones PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
      pdo_mysql \
      mbstring \
      intl \
      zip \
      bcmath \
      gd \
      exif \
      dom \
      fileinfo

# Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos primero archivos "de configuración" para aprovechar cache
COPY composer.json composer.lock ./
COPY package*.json ./



# Instalar dependencias PHP (sin dev)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

# Instalar dependencias JS
RUN npm install --no-audit --no-fund

# Copiamos el resto del proyecto
COPY . .

# Compilar assets con Vite
RUN npm run build

# Opcional: limpiar node_modules para que no viajen a la imagen final
RUN rm -rf node_modules


# =========================================================
# STAGE 2: RUNTIME (PHP-FPM + Nginx + Supervisor)
# =========================================================
FROM php:8.3-fpm-alpine AS runtime

# Paquetes para runtime
RUN apk add --no-cache \
    nginx supervisor curl bash tzdata ca-certificates \
    icu libzip oniguruma \
    libpng libjpeg-turbo freetype libxml2 \
 && update-ca-certificates

# Extensiones PHP (runtime)
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS \
      icu-dev libzip-dev oniguruma-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev \
    ; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install \
      opcache \
      intl \
      zip \
      bcmath \
      pdo_mysql \
      gd \
      exif \
      dom \
      fileinfo \
    ; \
    apk del .build-deps

# Opcache / ajustes básicos de PHP
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.jit=1255"; \
      echo "opcache.jit_buffer_size=64M"; \
      echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini \
 && { \
      echo "memory_limit=512M"; \
      echo "upload_max_filesize=50M"; \
      echo "post_max_size=50M"; \
      echo "expose_php=0"; \
      echo "display_errors=0"; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# Copiamos TODO el proyecto ya con vendor + public/build desde el build
COPY --from=build /var/www/html /var/www/html

# Permisos para Laravel
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# Nginx + Supervisor config (ya las tienes en .docker/)
RUN mkdir -p /run/nginx
COPY .docker/nginx.conf       /etc/nginx/nginx.conf
COPY .docker/site.conf        /etc/nginx/http.d/default.conf
COPY .docker/supervisord.conf /etc/supervisord.conf

# Puerto donde escuchará Nginx (Dokploy / Traefik apuntan aquí)
EXPOSE 8000

ENV APP_ENV=production \
    APP_DEBUG=false

CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
