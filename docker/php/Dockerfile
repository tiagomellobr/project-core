FROM php:8.3-fpm

# Instala as extensões necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    librabbitmq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo_mysql \
    && pecl install amqp redis \
    && docker-php-ext-enable amqp redis

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos da aplicação
COPY ../../ .

# Instala as dependências do Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala as dependências do Node.js e compila os assets
RUN apt-get install -y nodejs npm
RUN npm install
RUN npm run build

# Permissões
RUN chown -R www-data:www-data var

EXPOSE 8008

CMD ["php-fpm"]