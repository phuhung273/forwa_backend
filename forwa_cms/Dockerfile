# Build vendoor directory
FROM composer:latest as composerBuilder
RUN mkdir -p /app
WORKDIR /app
COPY . /app
    
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs



FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    libmagickwand-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Set working directory
RUN mkdir /var/www/forwa_cms
WORKDIR /var/www/forwa_cms

# Copy existing application directory contents
COPY . /var/www/forwa_cms

# Copy dependencies from builder
COPY --from=composerBuilder /app/vendor /var/www/forwa_cms/vendor

# Add user for application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Test permission
RUN usermod -a -G www-data www
RUN chown -R www:www-data /var/www/forwa_cms
RUN chmod -R 775 /var/www/forwa_cms/

RUN touch /var/log/fpm-php.www.log
RUN chown www-data:www-data /var/log/fpm-php.www.log

# Change current user to www
USER www