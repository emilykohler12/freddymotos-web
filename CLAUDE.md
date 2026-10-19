# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.


---

# Proyecto

Tienda online de repuestos y accesorios para motos (Freddy Motos) con panel de administración. Laravel 13, PHP 8.3, Blade + Tailwind 3 + Vite 5, Chart.js. Producción: Docker en Render, Postgres en Neon (`pdo_pgsql`); tests y local: SQLite. Las guías generales de Laravel, testing y Tailwind las cubre Laravel Boost (`AGENTS.md`, skills `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`); acá solo va lo propio del proyecto.

## Estructura

- **Modelos** (`app/Models`): `Product` (SoftDeletes), `Category`, `CategoryAttribute`, `ProductAttributeValue`, `Order`, `OrderItem`, `Customer`, `StockMovement`, `Supplier`, `SupplierPurchase`, `Mechanic`, `MechanicJob`, `Promotion`, `ShippingCompany`, `ShippingZone`, `Expense`, `ExpenseCategory`, `SiteSetting`, `WorkshopCategory`, `WorkshopInquiry`, `BusinessPhoto`, `PasswordResetCode`, `User`.
- **Controladores públicos**: Cart, Category, Checkout, Product, Workshop, MercadoPagoWebhook, `Auth/ClientAuth`, `Auth/PasswordReset`.
- **Panel admin** (`app/Http/Controllers/Admin`, rutas `admin.*` en `routes/web.php`, middleware `admin` = `EnsureUserIsAdmin`): Dashboard, Sales, Order, Product, Category, CategoryAttribute, Customer, Supplier, Promotion, Shipping, Expense, ExpenseCategory, StockMovement, ActivityLog (es la pantalla "Movimientos", no un registro de auditoría), Inquiry, Workshop, Mechanic, MechanicJob, SiteSetting.
- **Services/Support**: `Services/MercadoPagoService`, `Support/Cart` (carrito en sesión), `Support/Sorting` (orden sin tildes, distinto en SQLite y Postgres).
- Solo existe un Form Request (`CheckoutRequest`); el admin valida inline con `$request->validate()`.
- Tests: `tests/Feature/Admin/*` y `tests/Feature/PasswordResetTest.php` son el patrón a seguir. Solo existe `UserFactory`: los demás modelos se crean en los tests con `Model::create([...])` o por relación (`$customer->orders()->create(...)`).

## Comandos

Si `php` no está en el PATH (Windows/Laragon): `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe`.

- `composer setup` — instala, copia `.env.example`, key, **migra** y compila assets (no correrlo contra la base real).
- `composer dev` — `php artisan dev` (procesos de desarrollo; `php artisan dev:list` los muestra).
- `composer test` — limpia config y corre `php artisan test` (sqlite en memoria, ver `phpunit.xml`).
- Un solo test: `php artisan test --filter=NombreDelTest` o `php artisan test tests/Feature/Admin/OrderDeleteTest.php`.
- Estilo: `vendor/bin/pint` (corrige) / `vendor/bin/pint --test` (solo verifica). No hay `pint.json` y el código existente **no** sigue el preset por defecto (decenas de archivos fallan `--test`, sobre todo `concat_space`: el proyecto escribe `'a' . $b`). No corras Pint sobre archivos existentes: reescribe líneas que no tocaste. Sí sobre archivos nuevos (el hook `php-check` lo hace solo).
- Assets: `npm run build` (también lo hacen el Dockerfile y el CI).
- CI (`.github/workflows/ci.yml`): `npm run build` + `php artisan test` en push/PR a main. El CI no corre Pint.
- Deploy (`Dockerfile` + `start.sh`): `start.sh` corre `php artisan migrate --force` en cada arranque, usando `DB_URL_MIGRATE` (conexión directa a Neon) si existe, y luego `php artisan serve`. Toda migración llega a producción sola.
- `backup.yml`: backup semanal cifrado de la base de producción. Hay datos reales.

## Reglas del dominio (confirmadas en el código)

- **Stock**: se maneja solo con `Order::discountStock()`, `restoreStock()` y `abandon()`, siempre dentro de `DB::transaction`. Se descuenta al **crear** el pedido (`CheckoutController@store` web/WhatsApp y `Admin\OrderController@store` local); no espera al pago. `discountStock()` bloquea los productos y, si alguno no alcanza, lanza `InsufficientStockException` (revierte la transacción y se muestra como mensaje; no se reporta a Sentry). Al **cancelar** (`Admin\OrderController@update`, estado "cancelado") se devuelve el stock, una sola vez; reabrir un cancelado lo vuelve a descontar (y falla si ya no hay). Al eliminarlo (`@destroy`) se devuelve, salvo que ya estuviera cancelado. Si Mercado Pago falla al iniciar el pago, `abandon()` cancela el pedido y devuelve el stock.
- `StockMovement` (con `user_id`) se crea solo en ajustes manuales (`Admin\StockMovementController`: compra = suma, venta_local = resta, ajuste) y en compras a proveedor no canceladas (`SupplierController@storePurchase`). Los pedidos no generan `StockMovement`.
- **Estados separados** en `Order`: `status` (pendiente/enviado/entregado/cancelado; `pagado` es legacy), `payment_status` (pendiente/pagado/rechazado) y `refund_status`. Usar `isPaid()` y el scope `paid()` para incluir el legacy. `origin` (web/whatsapp/local) no cambia nunca; `payment_method` sí, cuando el admin marca como pagado un pedido de WhatsApp. Los pedidos locales nacen pagados.
- **Soft deletes**: `Product`, `Promotion`, `Expense`, `ExpenseCategory`, `Mechanic`, `MechanicJob`, `WorkshopCategory`. `Order` se borra de verdad.
- **Rate limit**: login (`throttle:login`) y reseteo de contraseña (`password-code`, `password-reset`) usan limiters definidos en `AppServiceProvider::boot()`; también `register` (registro de clientes) e `inquiry` (consultas del taller).
- **`SiteSetting`** usa `$fillable` explícito: una columna nueva hay que agregarla ahí o `fill()` la descarta sin avisar.
- **Sin registro de auditoría**: el modelo `ActivityLog` se eliminó (nada lo leía). La tabla `activity_logs` sigue existiendo en la base con datos viejos hasta que se agregue una migración que la borre, en un deploy posterior (ver `nueva-migracion-segura`). No usarla ni volver a escribir en ella. `Admin\ActivityLogController` es la pantalla "Movimientos" (pedidos, consultas, deudas) y no tiene relación con esa tabla.
- **SiteSetting**: una sola fila, siempre `SiteSetting::current()` (memoizado por request). Los datos de contacto no se inventan: sin cargar quedan en `null`.
- Orden alfabético en SQL: usar `Sorting::foldedName()`, no `ORDER BY` directo.
- Migraciones, storage y config corren contra Postgres (Neon) en producción: no usar SQL específico de SQLite.

## Pagos (Mercado Pago)

- Flujo: `CheckoutController@store` crea el pedido con `payment_method=mercadopago` → `MercadoPagoService::createPreference` (`external_reference` = id del pedido, `notification_url` = `webhook.mercadopago`) → redirige al `init_point` → MP vuelve a `checkout.success` y además llama a `POST /webhook/mercadopago` (excluido de CSRF en `bootstrap/app.php`).
- `MercadoPagoService::buildItems()` arma la preferencia con un ítem por línea (por su `subtotal`, ya con la promoción) más un ítem "Envío"; si no suman `Order.total` lanza error en vez de cobrar otro monto.
- El webhook consulta el pago a MP (`getPayment`) y no confía en el body; con la fila del pedido bloqueada: `approved` con monto y moneda iguales a los del pedido → `Order::markPaid` (idempotente por `isPaid()`; si el pedido estaba cancelado queda con `refund_status = pendiente`); monto distinto → no se marca pagado y se reporta; `refunded`/`charged_back` → `refund_status = reembolsado`; `rejected`/`cancelled` → `payment_status = rechazado` (salvo que ya esté pagado); si no se puede consultar a MP responde 503 para que reintente. Hoy **no valida la firma** `x-signature` de MP (pendiente, decisión del usuario).
- Sentry no captura el cuerpo de las requests (`max_request_body_size = none` en `config/sentry.php`).
- Credenciales: `MERCADOPAGO_ACCESS_TOKEN` y `MERCADOPAGO_CURRENCY` (`services.mercadopago`).
- **Nunca loguear ni reportar datos de pago ni claves** (tokens, respuestas completas de MP, `.env`). Ante errores usar `report($e)` solo con la excepción, sin payloads de pago.

## Antes de dar algo por terminado

1. Archivos PHP **nuevos**: `vendor/bin/pint --test <archivo>` (el repo existente no es Pint-limpio, ver Comandos). En archivos existentes mantené el estilo que ya tienen.
2. `php artisan test`
3. Si tocaste assets: `npm run build`
