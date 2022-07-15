FROM php:8.1-apache-bullseye
WORKDIR /opt
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY healthcheck.sh /opt/healthcheck.sh

RUN apt update && apt install nano -y

COPY docker-prepare.sh /usr/local/bin/docker-prepare.sh
RUN chmod a+x /usr/local/bin/docker-prepare.sh

RUN docker-php-ext-install mysqli pdo pdo_mysql

CMD docker-prepare.sh && apache2-foreground
