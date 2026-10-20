<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /carrito',
            'Disallow: /checkout',
            'Disallow: /login',
            'Disallow: /registro',
            'Disallow: /webhook',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => null],
            ['loc' => route('products.index'), 'lastmod' => null],
            ['loc' => route('categories.index'), 'lastmod' => null],
            ['loc' => route('workshop.index'), 'lastmod' => null],
        ]);

        Product::query()
            ->where('active', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(fn (Product $p) => $urls->push([
                'loc' => route('products.show', $p),
                'lastmod' => $p->updated_at?->toAtomString(),
            ]));

        // Se arma acá y no en Blade: con short_open_tag activo, Blade no compila la declaración "<?xml".
        $items = $urls->map(function (array $u) {
            $lastmod = $u['lastmod'] ? '<lastmod>' . e($u['lastmod']) . '</lastmod>' : '';

            return '<url><loc>' . e($u['loc']) . '</loc>' . $lastmod . '</url>';
        })->implode("\n");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . $items . "\n</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
