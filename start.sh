#!/bin/sh
set -e

echo "==> Corriendo migraciones..."
# Las migraciones necesitan la conexión DIRECTA a Neon (no la pooled/pgbouncer,
# que no banca bien transacciones multi-sentencia). El resto de la app usa la
# pooled (DB_URL de siempre) porque es mucho más rápida para conexiones cortas
# tipo request-por-request, que es como labura "php artisan serve".
DB_URL="${DB_URL_MIGRATE:-$DB_URL}" php artisan migrate --force
echo "==> Migraciones OK."

php artisan storage:link --force || echo "==> storage:link falló (no es grave si usás S3/R2), sigo igual."

echo "==> Arrancando en el puerto ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}" --no-reload
