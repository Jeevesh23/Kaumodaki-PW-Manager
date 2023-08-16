FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y git unzip zip curl
RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	    \
	&& docker-php-ext-install -j$(nproc) gd
RUN /bin/bash -c 'mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini'
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY entry-script.sh /usr/local/bin/
RUN /bin/bash -c 'chmod +x /usr/local/bin/entry-script.sh'
CMD ["/usr/local/bin/entry-script.sh"]