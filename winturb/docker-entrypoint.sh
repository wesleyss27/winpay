#!/bin/bash

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "[winturb] vendor não encontrado, rodando composer install..."
    cd /var/www/html && composer install --no-dev --prefer-dist || echo "[winturb] AVISO: composer install falhou, verifique os logs."
    echo "[winturb] composer install concluído."
fi

mkdir -p /var/www/html/uploads/videos \
         /var/www/html/uploads/capas \
         /var/www/html/uploads/anuncios \
         /var/www/html/logs

chown -R www-data:www-data /var/www/html/uploads /var/www/html/logs 2>/dev/null || true

exec apache2-foreground
