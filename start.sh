#!/bin/sh
set -e

echo "==> Corriendo migraciones..."
php artisan migrate --force
echo "==> Migraciones OK."

php artisan storage:link --force || echo "==> storage:link falló (no es grave si usás S3/R2), sigo igual."

echo "==> Arrancando en el puerto ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}" --no-reload
