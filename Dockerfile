# Use an official PHP runtime as a parent image
FROM php:8.3.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    wget \
    libxext6 \
    libxrender1 \
    libxtst6 \
    libxi6 \
    fontconfig \
    libfontconfig1 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip pdo pdo_pgsql

# Install OpenJDK from Adoptium
RUN mkdir -p /opt/java/openjdk \
    && curl -L -o /tmp/openjdk.tar.gz https://github.com/adoptium/temurin17-binaries/releases/download/jdk-17.0.2+8/OpenJDK17U-jdk_aarch64_linux_hotspot_17.0.2_8.tar.gz \
    && tar -xzf /tmp/openjdk.tar.gz -C /opt/java/openjdk --strip-components=1 \
    && rm /tmp/openjdk.tar.gz

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Symfony CLI
RUN curl -fsSL https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Install PhpStorm
RUN wget https://download.jetbrains.com/webide/PhpStorm-2023.2.5-aarch64.tar.gz -O /tmp/phpstorm.tar.gz && \
    tar -xzf /tmp/phpstorm.tar.gz -C /opt && \
    rm /tmp/phpstorm.tar.gz

# Create a user for PhpStorm
RUN useradd -ms /bin/bash user

# Set environment variables for PhpStorm
ENV DISPLAY=host.docker.internal:0
ENV GAMMA=1.0
ENV JAVA_HOME=/opt/java/openjdk
ENV PATH=$JAVA_HOME/bin:$PATH

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy existing application directory contents
COPY infonet-starwars /var/www/html

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV DATABASE_URL="postgresql://admin:admin@postgres2:5432/mydatabase"

# Install Symfony PHP framework dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Change current user to www-data
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Run Symfony commands to setup the database and start the server
CMD set -e && \
    php -r "if (!getenv('DATABASE_URL')) { echo 'DATABASE_URL environment variable is not set'; exit(1); }" && \
    composer install --no-interaction --prefer-dist --optimize-autoloader && \
    sleep 2 && \
    php bin/console doctrine:database:create --if-not-exists && \
    php bin/console doctrine:schema:update --force && \
    php bin/console make:migration --no-interaction && \
    php bin/console doctrine:migrations:migrate --no-interaction && \
    php bin/console starwars:import && \
    symfony server:start --no-tls --port=9000

