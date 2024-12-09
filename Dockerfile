FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libbz2-dev \
    libmcrypt-dev \
    libonig-dev \
    libpq-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHPUnit 9.6.5
RUN composer global require "phpunit/phpunit:9.6.5" --prefer-dist --no-progress --no-suggest

# Set PATH for global Composer packages
ENV PATH="/root/.composer/vendor/bin:$PATH"

# Set working directory
WORKDIR /var/www/html