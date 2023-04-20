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

COPY . /var/www/

WORKDIR /var/www

CMD ["tail -f /dev/null"]