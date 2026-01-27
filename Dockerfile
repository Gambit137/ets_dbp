FROM php:8.2-apache

# Instalacija ekstenzije za MySQL (ključno!)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Kopiranje datoteka
COPY . /var/www/html/

# Dozvole
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
