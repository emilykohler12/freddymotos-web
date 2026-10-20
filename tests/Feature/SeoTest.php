<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_tiene_metadatos_y_verificacion_de_google(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('google-site-verification', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('og:title', false)
            ->assertDontSee('noindex', false);
    }

    public function test_paginas_privadas_son_noindex(): void
    {
        foreach (['/login', '/registro'] as $url) {
            $this->get($url)->assertSuccessful()->assertSee('noindex', false)->assertDontSee('rel="canonical"', false);
        }
    }

    public function test_robots_y_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: ' . route('sitemap'), false);

        $activo = Product::create(['name' => 'Cadena', 'slug' => 'cadena', 'price' => 100, 'stock' => 1, 'active' => true, 'category' => 'Transmisión', 'brand' => 'Honda']);
        $inactivo = Product::create(['name' => 'Oculto', 'slug' => 'oculto', 'price' => 100, 'stock' => 1, 'active' => false, 'category' => 'Transmisión', 'brand' => 'Honda']);

        $this->get('/sitemap.xml')->assertOk()
            ->assertSee(route('products.show', $activo), false)
            ->assertDontSee(route('products.show', $inactivo), false);
    }
}
