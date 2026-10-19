---
name: pre-deploy
description: Checklist antes de desplegar Freddy Motos a producción (Render con Docker + Postgres en Neon). Usalo cuando el usuario diga "voy a desplegar", "mergear a main", "subir a producción", "release" o pida revisar si el cambio está listo para salir. No ejecuta el deploy ni toca la base real.
---

# Pre-deploy

Cómo sale hoy un cambio (según `Dockerfile`, `start.sh` y `.github/workflows/`):

- Un push/merge a `main` dispara el CI (`ci.yml`: `npm ci`, `npm run build`, `php artisan test` en PHP 8.3 con sqlite). El CI **no corre Pint**.
- El `Dockerfile` compila assets (Node 20, `npm ci` + `npm run build`), instala dependencias sin dev (`composer install --no-dev`) y arranca `start.sh`.
- `start.sh` ejecuta `php artisan migrate --force` en cada arranque (con `DB_URL_MIGRATE` si existe) y después `php artisan serve`. **Si la migración falla, el contenedor no levanta.**
- `backup.yml` hace un backup cifrado semanal de la base (también manual con `workflow_dispatch`).

Para guías generales de despliegue en la nube ver el skill de Laravel Boost `deploying-to-cloud`; acá solo lo propio de este proyecto. No ejecutar el deploy ni comandos contra la base real: solo verificar y reportar.

## Checklist

Corré los pasos en orden y reportá cada uno como ✅ / ❌ / ⚠️ con evidencia. Si `php` no está en el PATH usar `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe`.

1. **Alcance del cambio**: `git status`, `git log main..HEAD --oneline` y `git diff main...HEAD --stat`. No debe haber cambios sin commitear ni archivos ajenos (`.env`, volcados, `database/database.sqlite`).
2. **Estilo**: `vendor/bin/pint --test` (el CI no lo corre, así que acá es la única barrera).
3. **Tests**: `php artisan test` (sqlite en memoria; lo mismo que hace el CI).
4. **Assets**: `npm run build` sin errores (Vite 5 + Tailwind 3). Si tocaste Blade/Tailwind, confirmar que las clases nuevas aparecen en el build.
5. **Migraciones pendientes**: listar las nuevas con `git diff --name-only main...HEAD -- database/migrations` (no correr `migrate:status` ni `migrate` contra la base real). Por cada una aplicar el skill `nueva-migracion-segura`: columnas nullable/default, nada que borre algo que el código aún use, `down()` coherente, compatible con Postgres. Recordar que se aplicará **sola** al arrancar el contenedor. Si es riesgosa, pedir antes un backup manual (workflow *Backup semanal de la base* → *Run workflow*) y esperar que termine.
6. **Variables de entorno nuevas**: buscar usos nuevos de `env(` / `config(` en el diff y claves nuevas en `config/*.php`; cada una debe estar en `.env.example` (sin valores reales) y avisarle al usuario cuáles hay que cargar en el panel de Render **antes** del deploy (por ejemplo `MERCADOPAGO_*`, `AWS_*`, `MAIL_*`, `SENTRY_*`, `DB_URL`, `DB_URL_MIGRATE`). Nunca leer ni imprimir el `.env` real.
7. **Sentry**: confirmar que `SENTRY_LARAVEL_DSN` y `SENTRY_TRACES_SAMPLE_RATE` están previstos en `.env.example`/Render, que `bootstrap/app.php` conserva `Sentry\Laravel\Integration::handles($exceptions)`, y que el cambio no agrega datos personales ni de pago a `report()`/contexto/breadcrumbs. Tras el deploy, mirar que no aparezcan errores nuevos.
8. **Pagos y seguridad** (si el cambio toca checkout, pedidos, stock, rutas admin o auth): pasar por los subagentes `revisor-pagos-pedidos` y/o `revisor-seguridad-laravel` y resolver o aceptar explícitamente los hallazgos críticos y altos.
9. **Docker/arranque**: si el cambio toca `Dockerfile`, `start.sh`, extensiones PHP o `composer.json`/`package.json`, verificar que el `composer.lock`/`package-lock.json` esté actualizado (se usan `composer install` y `npm ci`) y que nada dependa de paquetes de `require-dev` en runtime (la imagen instala `--no-dev`).
10. **Rama y PR**: no pushear directo a `main` (hay un hook que lo bloquea); el cambio sale por PR y esperar el CI verde antes de mergear.

## Después del deploy (para recordarle al usuario)

- Revisar en los logs de Render que aparezcan `==> Migraciones OK.` y `==> Arrancando en el puerto …`.
- Abrir la home, un producto, el carrito y el login del panel; si se tocó pagos, hacer una compra de prueba con credenciales de **test** de Mercado Pago.
- Revisar Sentry unos minutos.
- Si algo sale mal: redeploy del commit anterior en Render. Las migraciones **no se revierten solas**; una migración que ya corrió requiere una migración nueva que corrija (o restaurar el backup cifrado, procedimiento en el encabezado de `backup.yml`).

## Reporte final

Resumí en una tabla: paso, estado, nota. Cerrá con "Listo para desplegar" o la lista de bloqueantes.
