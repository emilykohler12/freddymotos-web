# Freddy Motos

Tienda online de repuestos y accesorios para motos, con catálogo público, carrito, checkout con Mercado Pago y un panel de administración completo para gestionar el negocio.

## Funcionalidades

**Sitio público**
- Catálogo de productos por categoría, con búsqueda, filtros y modelos de moto compatibles.
- Carrito de compras y checkout (retiro en local o envío), disponible solo para usuarios registrados.
- Pago online con Mercado Pago o coordinación de pago por WhatsApp.
- Promociones (descuentos por porcentaje, monto fijo o Nx M) aplicadas automáticamente en el carrito.
- Cuentas de cliente, con recuperación de contraseña por código de 6 dígitos enviado por correo.

**Panel de administración**
- Dashboard con KPIs y gráficos, filtrables por día, semana, mes o año.
- Gestión de productos, categorías, promociones, pedidos, clientes, proveedores y envíos.
- Carga manual de pedidos coordinados por WhatsApp (cliente, envío, productos y precio a mano).
- Inventario: altas y bajas de stock por compra o venta en el local, sin editar el stock a mano en cada producto.
- Gastos organizados por categoría y "Otros ingresos", con pagos únicos/anuales que se archivan solos una vez pagados.
- Historial de movimientos y notificaciones (pedidos por WhatsApp, compras web, stock bajo, pagos y reembolsos pendientes).
- Configuración del negocio (datos de contacto, horarios, medios de pago, moneda e impuestos, logo).

## Stack técnico

- [Laravel 13](https://laravel.com) (PHP 8.3+)
- [Tailwind CSS 3](https://tailwindcss.com) + [Vite](https://vitejs.dev)
- Blade
- Mercado Pago (pagos) y Mailjet (envío de correos transaccionales)

## Requisitos

- PHP 8.3+
- Composer
- Node.js y npm

## Instalación

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan storage:link

npm run build
php artisan serve
```

Completá en `.env` las credenciales de Mercado Pago y Mailjet antes de usar pagos o el envío de correos en producción.

## Autoría

Creado por [Emily Kohler](mailto:emilynoralikohler@gmail.com).
