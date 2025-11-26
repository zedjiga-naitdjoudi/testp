# Utilise l'image officielle PHP + Apache
FROM php:8.2-apache

# Installer les dépendances système + outils de compilation
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    unzip \
    git \
    libyaml-dev \
    $PHPIZE_DEPS \
 && docker-php-ext-install pdo pdo_pgsql pgsql \
 && pecl channel-update pecl.php.net \
 && pecl install yaml \
 && docker-php-ext-enable yaml \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*


# (optionnel) pour Apache
WORKDIR /var/www/html
EXPOSE 80
