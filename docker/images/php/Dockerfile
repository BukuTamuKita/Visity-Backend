FROM php:7.4-fpm-alpine

# lumen packages
RUN docker-php-ext-install tokenizer mysqli pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

