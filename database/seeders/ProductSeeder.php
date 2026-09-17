<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Casco integral MT Thunder 4',
                'category' => 'Cascos',
                'brand' => 'MT Helmets',
                'compatible_model' => 'Uso universal',
                'description' => 'Casco integral con visor antirrayas, pantalla solar interna y forro desmontable lavable. Certificación ECE 22.06.',
                'price' => 145000,
                'stock' => 12,
                'is_featured' => true,
            ],
            [
                'name' => 'Pastillas de freno delanteras cerámicas',
                'category' => 'Frenos',
                'brand' => 'Brembo',
                'compatible_model' => 'Honda CB 250 Twister / XR 250',
                'description' => 'Juego de pastillas cerámicas de alto rendimiento, baja generación de polvo y frenada progresiva en seco y mojado.',
                'price' => 28900,
                'stock' => 40,
                'is_featured' => true,
            ],
            [
                'name' => 'Aceite sintético 10W-40 moto 4T (1L)',
                'category' => 'Lubricantes',
                'brand' => 'Motul',
                'compatible_model' => 'Motores 4 tiempos con embrague húmedo',
                'description' => 'Aceite 100% sintético para motores 4T. Protege el embrague, la caja y el motor en altas exigencias. Norma JASO MA2.',
                'price' => 18500,
                'stock' => 60,
                'is_featured' => true,
            ],
            [
                'name' => 'Kit de transmisión (cadena + coronas)',
                'category' => 'Transmisión',
                'brand' => 'DID',
                'compatible_model' => 'Yamaha YBR 125 / Factor 125',
                'description' => 'Kit completo con cadena reforzada con o-ring, corona trasera y piñón de ataque. Mayor durabilidad y menor estiramiento.',
                'price' => 52000,
                'stock' => 15,
                'is_featured' => false,
            ],
            [
                'name' => 'Filtro de aire deportivo lavable',
                'category' => 'Motor',
                'brand' => 'K&N',
                'compatible_model' => 'Bajaj Rouser 200 NS / 200 RS',
                'description' => 'Filtro de aire de algodón lavable y reutilizable. Mejora el flujo de admisión y se limpia con kit de mantenimiento.',
                'price' => 34500,
                'stock' => 9,
                'is_featured' => false,
            ],
            [
                'name' => 'Batería de gel 12V 7Ah',
                'category' => 'Eléctrico',
                'brand' => 'Yuasa',
                'compatible_model' => 'Motos 125cc a 250cc con arranque eléctrico',
                'description' => 'Batería sellada de gel, libre de mantenimiento, resistente a vibraciones y con buena capacidad de arranque en frío.',
                'price' => 41000,
                'stock' => 0,
                'is_featured' => false,
            ],
            [
                'name' => 'Cubierta trasera 130/70-17 sport touring',
                'category' => 'Neumáticos',
                'brand' => 'Pirelli',
                'compatible_model' => 'Rodado 17 trasero (deportivas y naked)',
                'description' => 'Neumático radial con excelente agarre en mojado y kilometraje parejo. Ideal para uso mixto calle y ruta.',
                'price' => 96000,
                'stock' => 7,
                'is_featured' => true,
            ],
            [
                'name' => 'Guantes de cuero con protecciones',
                'category' => 'Indumentaria',
                'brand' => 'Alpinestars',
                'compatible_model' => 'Uso universal',
                'description' => 'Guantes de cuero perforado con refuerzo en nudillos y palma, cierre de muñeca y dedos táctiles para el celular.',
                'price' => 47500,
                'stock' => 22,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'image_path' => null, // el admin sube la imagen luego; mientras se muestra el placeholder
                ]),
            );
        }
    }
}
