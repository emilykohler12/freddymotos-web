# Sección Hero — Home

## Ver el diseño ahora (sin Laravel)

Abrí **`preview.html`** en el navegador. Es un espejo del componente con Tailwind por CDN.
Probá achicar la ventana para ver el comportamiento mobile (menú hamburguesa con checkbox, sin JS).

## Archivos creados

| Archivo | Qué es |
|---|---|
| `resources/views/components/hero.blade.php` | Componente Hero (`<x-hero />`) |
| `resources/views/home.blade.php` | Página Home que usa el hero |
| `resources/views/layouts/app.blade.php` | Layout base |
| `routes/web.php` | Ruta `/` → `home` |
| `tailwind.config.js` | Paleta de la tienda (tokens `marca-*`) |
| `postcss.config.js`, `vite.config.js`, `package.json` | Build de assets |
| `resources/css/app.css`, `resources/js/app.js` | Entradas de Vite |
| `preview.html` | Preview rápido standalone |

## Integrar en un proyecto Laravel

Este directorio todavía **no tiene el framework**. Cuando lo tengas:

```bash
# crear el proyecto (o copiar estos archivos sobre uno existente)
composer create-project laravel/laravel .

# assets
npm install
npm run dev        # o: npm run build

# servir
php artisan serve
```

Los archivos de arriba se pueden copiar tal cual sobre un `laravel new` (pisan
`routes/web.php`, `resources/views/`, config de Tailwind/Vite).

## Paleta (tokens Tailwind)

`marca-amarillo` `marca-rojo` `marca-negro` `marca-blanco` `marca-gris-claro`
`marca-gris-oscuro` `marca-mostaza` `marca-bordo`

Ajustá los HEX en `tailwind.config.js` (y en `preview.html`) si querés afinar los tonos.

## Pendiente / placeholders

- Todos los textos están marcados con `{{-- PLACEHOLDER --}}`.
- La imagen es un SVG de placeholder. Para foto real, reemplazá el `<svg>` por:
  ```blade
  <img src="{{ asset('images/hero-moto.png') }}" alt="Moto destacada"
       class="relative mx-auto w-full max-w-lg drop-shadow-2xl">
  ```
- Los links de navegación apuntan a `#`.
