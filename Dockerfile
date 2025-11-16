# Dockerfile pour Symfony
FROM php:8.4-fpm-alpine

# Installation des dépendances système
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Installation des extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    opcache

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances en premier (pour le cache Docker)
COPY composer.json composer.lock symfony.lock ./

# Installer les dépendances Composer
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copier le reste de l'application
COPY . .

# Générer l'autoloader optimisé
RUN composer dump-autoload --optimize

# Créer les répertoires nécessaires avec les bonnes permissions
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data var/

# Configuration PHP pour le développement
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/docker-php-uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/docker-php-uploads.ini

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
