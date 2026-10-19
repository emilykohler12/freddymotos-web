---
name: nuevo-crud-admin
description: Crea un CRUD nuevo en el panel de administración de Freddy Motos siguiendo la convención real del proyecto (modelo + migración, controlador en Admin, validación, rutas, vistas Blade y test; ActivityLog opcional). Usalo cuando se pida "agregar una sección/entidad/ABM/CRUD al panel admin" (por ejemplo clientes, marcas, vehículos). No lo uses para modificar un CRUD existente.
---

# Nuevo CRUD del panel admin

Convención sacada de `Supplier` (CRUD completo con páginas propias) y `Mechanic` (CRUD liviano dentro de una pestaña). Antes de escribir, releé el que más se parezca a lo pedido:

- Completo (index / create / edit / show): `app/Http/Controllers/Admin/SupplierController.php`, `app/Models/Supplier.php`, `resources/views/admin/suppliers/{index,form,show}.blade.php`.
- Liviano (store / update / destroy dentro de una pestaña de otra pantalla): `app/Http/Controllers/Admin/MechanicController.php`, `app/Models/Mechanic.php`, `resources/views/admin/workshop/index.blade.php`.

Las buenas prácticas generales de Laravel y de testing las cubren los skills de Laravel Boost `laravel-best-practices` y `testing-best-practices`; acá solo va lo propio del proyecto. Si el CRUD agrega una tabla, aplicá también el skill `nueva-migracion-segura` (hay datos reales en producción y `start.sh` migra solo en cada deploy).

## Checklist

1. **Definir** con el usuario: nombre (modelo en inglés singular, rutas/URLs/etiquetas en español), campos, si necesita borrado lógico, página de detalle (`show`) o solo pestaña. Si algo es ambiguo, preguntar antes de generar.
2. **Modelo** en `app/Models/`: `$fillable` explícito (nunca `$guarded = []`), `casts()` como método, relaciones tipadas (`HasMany`, `BelongsTo`), accessors `getXxxAttribute`. `use SoftDeletes` si la entidad se referencia desde otras con datos históricos (así lo hacen `Mechanic`, `Promotion`, `Expense`); si no, borrado real.
3. **Migración** (ver `nueva-migracion-segura`): `php artisan make:migration create_xxx_table`, clase anónima, `up()` con `Schema::create`, `down()` con `Schema::dropIfExists`. Columnas opcionales `nullable()`.
4. **Factory**: el proyecto hoy solo tiene `UserFactory`; los tests crean modelos con `Model::create([...])`. No crear factory salvo que el usuario la pida o la use más de un test.
5. **Controlador** en `app/Http/Controllers/Admin/XxxController.php`:
   - Métodos `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` (o solo `store/update/destroy` si es liviano).
   - Validación **inline** con `$request->validate([...])`, centralizada en un `private function validated(Request $request, ?Model $model = null): array` que se reutiliza en store y update (así lo hace `SupplierController`). Reglas típicas: `['required','string','max:150']`, `unique:tabla,columna,{id}` al editar, teléfono `regex:/^[0-9+()\s-]{6,40}$/`, email `['nullable','email','max:160']`, mensajes en español para `unique`. No existe una convención de Form Requests en el admin (solo `CheckoutRequest` en el checkout público); seguir el patrón inline.
   - Listado: `paginate(15)->withQueryString()`, búsqueda con `Sorting::fold($search)` y orden alfabético con `orderByRaw(Sorting::foldedName('name') . ' ASC')` (funciona en SQLite y Postgres; no usar `ORDER BY name` directo).
   - Respuestas: `redirect()->route('admin.xxx.index')->with('status', 'Xxx creado.')`; errores de negocio con `->with('error', '...')`. Si el registro tiene dependientes, bloquear el borrado con mensaje (como `Supplier::destroy`).
   - Varias escrituras relacionadas (registro + stock + movimientos) en `DB::transaction`.
6. **ActivityLog (opcional)**: hoy solo se usa para pedidos (`Order`, `CheckoutController`, `Admin\OrderController`); `Supplier` y `Mechanic` no lo hacen y **ninguna vista del panel lee `activity_logs`** (solo se escribe). Registrá altas/cambios/bajas únicamente si el usuario lo pide, con `ActivityLog::log('xxx_creado', "Xxx «{$x->name}» creado", $x)` **después** de la transacción y `type` en snake_case, avisando que no se muestra en ningún lado. Nunca escribir contraseñas, tokens ni datos de pago en la descripción.
7. **Rutas** en `routes/web.php`, dentro del grupo `Route::prefix('admin')->name('admin.')` → `Route::middleware('admin')`, con URLs en español y bloque comentado `/* Xxx */`, igual que proveedores: `xxxs`, `xxxs/nuevo`, `xxxs/{xxx}/editar`, nombres `admin.xxxs.index|create|store|show|edit|update|destroy`. Las rutas fijas (`nuevo`) van antes que las que llevan `{param}`. Importar el controlador con alias `use ...\Admin\XxxController as AdminXxxController;` en orden alfabético.
8. **Vistas** en `resources/views/admin/xxxs/` (`index`, `form` compartido para crear/editar con `$model->exists`, y `show` si aplica): `@extends('layouts.admin')`, `@section('title')`, `@section('page-heading')`, `@section('content')`. Copiar clases Tailwind de `suppliers/form.blade.php` (`marca-amarillo`, `marca-rojo`, `marca-gris-oscuro`, etc.), `@csrf`/`@method('PUT')`, `old('campo', $model->campo)`, `$errors->first()`, botón de borrado con `data-confirm="¿Eliminar …?"`, buscador con `data-autosubmit`, y `{{ $items->links() }}`. Escapar siempre con `{{ }}`.
9. **Menú**: agregar el ítem en el arreglo de navegación de `resources/views/layouts/admin.blade.php` (`label`, `route`, `active` con `admin.xxxs.*`, `icon`).
10. **Test** en `tests/Feature/Admin/XxxTest.php` (delegar en el subagente `escritor-tests` si conviene): `RefreshDatabase`, admin con `User::factory()->create(['is_admin' => true])`, crear con `Model::create`, cubrir crear, editar, borrar, validación inválida, 403 para usuario no admin y redirección a login para invitados (y el `ActivityLog` solo si lo registraste).
11. **Verificar**: `php artisan test`, `vendor/bin/pint --test` solo sobre los archivos PHP nuevos (el código existente no es Pint-limpio), y revisar la pantalla con `composer dev` si hay vistas nuevas. Pasar por `revisor-seguridad-laravel` si el CRUD sube archivos o toca permisos.

## No hacer

- No usar `$request->all()` en `create`/`update`; usar el array validado.
- No dejar rutas admin fuera del middleware `admin`.
- No editar migraciones existentes ni correr migraciones contra la base real para probar: los tests usan sqlite en memoria.
