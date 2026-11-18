FROM php:8.5-apache

# --- System libraries required by the PHP extensions below

# Install system dependencies and PHP extensions Laravel needs   
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo_mysql \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/* 

# --- Apache: rewrite + headers are required/handy for Laravel
RUN a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
# --- Laravel must be served from /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
#RUN chmod +x /usr/local/bin/entrypoint.sh

# --- Composer
#COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80