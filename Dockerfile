FROM php:8.0-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libpq-dev \
    libicu-dev \
    libxml2-dev

# Installez les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring exif pcntl bcmath gd intl xml curl

# Installez Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définissez le répertoire de travail
WORKDIR /var/www/html

# Copiez les fichiers de votre application
COPY . .
COPY .env.example .env

# Installez les dépendances PHP avec Composer
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist



# Exposez le port utilisé par votre application
EXPOSE 8000

# Commande pour démarrer l'application Lumen
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
