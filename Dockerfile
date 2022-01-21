FROM php:8.1-alpine

RUN apk update \
    && apk add chromium \
    && apk add libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install sockets \
    && rm -rf /var/cache/apk/*

COPY . /app
WORKDIR /app

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/d4525508b60af43a52f274d70315bfed4d671fd3/web/installer -O - -q | php -- --quiet \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer
RUN composer install -o

WORKDIR /app/public

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80"]
