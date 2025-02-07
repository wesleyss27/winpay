# Use a imagem oficial do PHP com Apache
FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y libpq-dev libsqlite3-dev

# PDO Driver
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite

# Porta do apache
EXPOSE 80

# Copia arquivos da aplicação para o diretório raiz do Apache
COPY ./ /var/www/html

# Permissões dos fopens
RUN chown -R www-data:www-data /var/www/html/logs
RUN chmod -R 0755 logs/
RUN chmod -R 0755 logs/*
RUN chmod -R 0755 source/App/Bling/
RUN chmod 664 webhook-mercadopago.txt
RUN chmod 664 bling.txt


# Ativa os módulos Apache para rewrite
# RUN a2enmod rewrite
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

