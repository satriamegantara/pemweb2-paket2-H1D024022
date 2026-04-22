FROM php:8.3-cli

# Install dependencies sistem + GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install extension penting Laravel
RUN docker-php-ext-install pdo pdo_mysql bcmath

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080