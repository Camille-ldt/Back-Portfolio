FROM php:8.1-cli

# Installer les dépendances nécessaires pour Composer et Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip

# Copier les fichiers composer.json et composer.lock pour installer les dépendances
COPY composer.json composer.lock /var/www/

# Se déplacer dans le répertoire de travail
WORKDIR /var/www/

# Installer les dépendances PHP avec Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction

# Copier tout le reste du projet
COPY . /var/www/

# Exposer le port de votre application
EXPOSE 80

# Définir le répertoire de travail
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www"]
