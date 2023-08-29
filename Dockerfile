FROM php:8.2

COPY . /var/www/html/

# ENV APACHE_DOCUMENT_ROOT /var/www/html/amr-api/public

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable pdo_mysql && \
    docker-php-ext-install intl && \
    docker-php-ext-enable intl

# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

WORKDIR /var/www/html/

RUN apt-get update
RUN apt-get install zip unzip -y
RUN apt-get install git -y

RUN composer -v
RUN cd /var/www/html/
RUN composer validate
RUN composer install

RUN apt-get install wget -y
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN export PATH="$HOME/.symfony5/bin:$PATH"
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN symfony -V
CMD php bin/console doctrine:migrations:migrate && symfony server:start 
EXPOSE 80