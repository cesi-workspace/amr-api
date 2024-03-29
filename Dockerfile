FROM php:8.2

COPY . /var/www/html/

# ENV APACHE_DOCUMENT_ROOT /var/www/html/amr-api/public

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable pdo_mysql 

# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

WORKDIR /var/www/html/

RUN apt-get update
RUN apt-get install zip unzip -y
RUN apt-get install git -y

# Installation de l'extension "intl"
RUN apt-get install -y libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN composer -v
RUN cd /var/www/html/
RUN composer validate
RUN composer install
RUN composer require symfony/intl
RUN apt-get install wget -y
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN export PATH="$HOME/.symfony5/bin:$PATH"
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN symfony -V
RUN mkdir config/jwt
RUN openssl genpkey -algorithm RSA -out config/jwt/private.pem
RUN openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
RUN echo "JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem" > .env.local
RUN echo "JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem" >> .env.local
CMD php bin/console doctrine:migrations:migrate && symfony server:start 
EXPOSE 80