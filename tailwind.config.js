/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        // Vistas de paginación de Laravel (para que Tailwind compile sus clases)
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Paleta de la tienda. Usar SIEMPRE estos tokens en el sitio.
                marca: {
                    amarillo: '#F5C518',
                    rojo: '#D22F27',
                    negro: '#141414',
                    blanco: '#FFFFFF',
                    'gris-claro': '#F2F2F2',
                    'gris-oscuro': '#2A2A2A',
                    mostaza: '#BC7C1A',
                    bordo: '#6E1423',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
