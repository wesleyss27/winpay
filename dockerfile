# Use a imagem oficial do PHP com Apache
FROM php:7.4-apache

# Instala pacotes necessários
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    libzip-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_sqlite zip

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ativa mod_rewrite do Apache
RUN a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia o código-fonte da aplicação
COPY . /var/www/html

# Permissões
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs && \
    chmod -R 0755 /var/www/html/logs && \
    chmod -R 0755 /var/www/html/source/App/Bling/ && \
    chmod 664 /var/www/html/webhook-mercadopago.txt || true && \
    chmod 664 /var/www/html/bling.txt || true

EXPOSE 80
