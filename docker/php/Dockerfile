FROM php:8.3-fpm-alpine

# Arguments et variables d'environnement
ARG WORKDIR=/var/www/html
ENV WORKDIR=${WORKDIR}

# Dépendances système pour extensions PHP
RUN apk update && apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    $PHPIZE_DEPS

# Configuration et installation des extensions GD, PDO MySQL, BCMath, Intl, OPcache
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Installation de l'extension Redis via PECL
RUN pecl install redis && docker-php-ext-enable redis

# Installation de Composer v2 officiel
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configuration du répertoire de travail
WORKDIR ${WORKDIR}

# Sécurité & Permissions
RUN addgroup -g 1000 -S cypress && \
    adduser -u 1000 -S cypress -G cypress

USER root

EXPOSE 9000

CMD ["php-fpm"]
