FROM php:8.1-cli

LABEL maintainer="Aleksandr Drobotik <drobotiksbox@gmail.com>"

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        wget \
    && docker-php-ext-install zip pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN wget https://phar.phpunit.de/phpunit-9.phar && \
    chmod +x phpunit-9.phar && \
    mv phpunit-9.phar /usr/bin/phpunit

RUN wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O php-cs-fixer && \
    chmod a+x php-cs-fixer && \
    mv php-cs-fixer /usr/local/bin/php-cs-fixer

COPY . /var/www/

WORKDIR /var/www
