---
name: nueva-migracion-segura
description: Reglas para escribir migraciones de Laravel que no rompan los datos reales de producción (Postgres en Neon, con migración automática en cada deploy), y cómo probarlas en sqlite. Usalo siempre que se cree o se piense una migración nueva, se agregue/cambie/borre una columna o índice, o se pida "agregar un campo" a una tabla existente.
---

# Migraciones seguras

Contexto real del proyecto:

- Producción usa **Postgres (Neon)**; local y tests usan **SQLite** (`phpunit.xml`: `sqlite` en memoria). La migración tiene que funcionar en los dos.
- `start.sh` corre `php artisan migrate --force` en **cada arranque del contenedor** (con `DB_URL_MIGRATE`, la conexión directa a Neon). Mergear a `main` = migrar producción. No hay paso manual intermedio.
- Hay datos reales de pedidos, clientes y stock, y un backup semanal (`.github/workflows/backup.yml`). Una migración destructiva no se deshace sola.
- Las migraciones ya existentes son intocables (el hook `guard-edit` bloquea editarlas): cualquier corrección va en una migración nueva. Las guías generales (índices, claves foráneas, tipos) están en el skill de Laravel Boost `laravel-best-practices`; acá solo las reglas de seguridad de datos.

## Reglas

1. **Una migración nueva por cambio.** Nunca modificar una existente. Nombre descriptivo: `add_xxx_to_yyy_table`, `create_zzz_table`, `make_xxx_nullable_on_yyy`. Clase anónima `return new class extends Migration`, como las actuales.
2. **Columnas nuevas en tablas con datos: `nullable()` o con `default()`.** Una columna `NOT NULL` sin default falla en Postgres si la tabla ya tiene filas. Si el valor definitivo es obligatorio, hacerlo en dos deploys: (1) agregar nullable/default y rellenar; (2) en una migración posterior, endurecer.
3. **No borrar ni renombrar columnas/tablas en el mismo deploy que el código que deja de usarlas.** Mientras se despliega, el código viejo sigue corriendo contra el esquema nuevo. Secuencia segura: deploy 1 deja de usar la columna; deploy 2 (otra migración) la elimina. Preferir no borrar si no estorba.
4. **Rellenos de datos (backfill)** dentro de la migración solo si son chicos, idempotentes y con `DB::table(...)->update(...)` (sin modelos Eloquent, que pueden cambiar después). Para volúmenes grandes, hacerlo por tandas.
5. **`down()` coherente**: revierte exactamente lo que hace `up()` (`dropColumn`, `dropIfExists`, `dropSoftDeletes`). Si es irreversible (datos transformados o borrados), decirlo en un comentario y lanzar una excepción en `down()` en lugar de dejar un `down()` que miente. Ojo: `migrate:rollback` está bloqueado por hook fuera de pruebas.
6. **Cambios de tipo o de nulabilidad (`->change()`)**: SQLite y Postgres se comportan distinto. Ver `2026_09_27_000002_make_moto_and_problema_nullable_on_mechanic_jobs.php`: usa `Schema::...->change()` en SQLite y `ALTER TABLE ... DROP NOT NULL` en Postgres según `Schema::getConnection()->getDriverName()`, porque no está `doctrine/dbal`. Copiar ese patrón.
7. **Nada específico de un motor sin rama para el otro**: ni `ILIKE`, ni `jsonb`, ni funciones de Postgres sin alternativa en SQLite (y viceversa). Para ordenar sin tildes usar `App\Support\Sorting`.
8. **Índices y claves foráneas**: agregar índices solo donde haya consultas que los usen; claves foráneas con `nullOnDelete()` o `restrictOnDelete()` según el caso (no `cascadeOnDelete()` sobre pedidos, clientes o productos con historial). Productos y otras entidades con historial usan `SoftDeletes`.
9. **Transacciones**: Neon *pooled* no soporta bien transacciones multi-sentencia; por eso las migraciones usan la conexión directa (`DB_URL_MIGRATE`). No cambiar eso ni agregar migraciones que dependan de la conexión pooled.
10. **Variables o config nuevas** que necesite el cambio van a `.env.example` (nunca al `.env` real).

## Cómo probarla (sin tocar la base real)

No correr `php artisan migrate`, `migrate:fresh`, `db:wipe` ni `migrate:rollback` contra la base configurada en `.env`. Si `php` no está en el PATH usar `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe`.

1. **Suite completa en sqlite en memoria** (aplica todas las migraciones desde cero con `RefreshDatabase`): `php artisan test`.
2. **Migrar en limpio y revertir solo en sqlite descartable**, sin `.env` real:
   `DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan migrate:fresh --env=testing`
   (el hook `guard-bash` solo permite estos comandos si llevan `--env=testing` y `DB_CONNECTION=sqlite`). Para probar `down()`, usar un test que ejecute `Artisan::call('migrate:rollback', ['--step' => 1])` sobre `RefreshDatabase` en sqlite.
3. **Datos existentes**: si la migración transforma filas, escribir un test que cree filas con el esquema anterior (insertando con `DB::table`), ejecute la migración y verifique el resultado.
4. **Postgres**: no se puede probar localmente sin una base Postgres. Si el cambio usa SQL específico (rama `pgsql`), revisarlo con cuidado línea por línea, o pedirle al usuario que lo pruebe contra una rama/branch de Neon, nunca contra la base principal.
5. Antes de mergear una migración riesgosa: pedirle al usuario que dispare manualmente el workflow *Backup semanal de la base* (`workflow_dispatch`) y confirme que terminó bien.

## Checklist rápido

- [ ] Archivo nuevo, no editado; nombre claro
- [ ] Columnas nuevas nullable o con default
- [ ] Nada se borra/renombra mientras el código aún lo usa
- [ ] `down()` revierte `up()` (o falla explícitamente)
- [ ] Funciona en sqlite y en Postgres (rama por driver si hace falta)
- [ ] `php artisan test` pasa y `vendor/bin/pint --test <la migración nueva>` no marca nada
- [ ] `.env.example` actualizado si hay variables nuevas
