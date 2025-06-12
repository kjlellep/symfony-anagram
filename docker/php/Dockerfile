FROM php:8.2-cli

# Install extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libzip-dev libxml2-dev libonig-dev libpq-dev libpng-dev libjpeg-dev libfreetype6-dev zlib1g-dev libcurl4-openssl-dev \
    && docker-php-ext-install intl mbstring zip pdo pdo_mysql xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
