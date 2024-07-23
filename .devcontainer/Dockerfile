FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    mariadb-client \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd mysqli soap

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set php memory limit to 4G
RUN echo "memory_limit=4G" > /usr/local/etc/php/conf.d/memory-limit.ini

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Copy your WordPress files to the image
COPY ./ /var/www/html

# Fix permissions
RUN chown -R www-data:www-data /var/www/html