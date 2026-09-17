<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Configuración del sitio. Siempre una sola fila (id = 1).
 * Se lee con SiteSetting::current() (cacheado + memoizado por request).
 */
class SiteSetting extends Model
{
    protected $guarded = [];

    private static ?self $memo = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }

    /** Valores por defecto razonables mientras el admin no cargó nada. */
    public static function defaults(): array
    {
        return [
            'nombre_local' => 'Freddy Motos',
            'telefono' => '+54 351 000-0000',
            'whatsapp' => '5493510000000',
            'email' => 'ventas@freddymotos.com',
            'direccion' => 'Av. Siempreviva 742, Córdoba',
            'horario_atencion' => 'Lunes a viernes de 9 a 18, sábados de 9 a 13',
            'instagram_url' => 'https://instagram.com/freddymotos',
            'facebook_url' => 'https://facebook.com/freddymotos',
            'logo_path' => null,
            'moneda' => 'ARS',
            'tax_rate' => null,
            'payment_methods' => ['mercadopago', 'whatsapp'],
            'banco' => null,
            'cbu_alias' => null,
            'titular_cuenta' => null,
            'mp_public_key' => null,
            'mp_access_token' => null,
        ];
    }

    protected function casts(): array
    {
        return [
            'payment_methods' => 'array',
            'tax_rate' => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        return static::$memo ??= static::resolve();
    }

    private static function resolve(): self
    {
        try {
            return Cache::rememberForever(
                'site_settings',
                fn () => static::query()->first() ?: new static(static::defaults()),
            );
        } catch (\Throwable) {
            // La tabla todavía no existe (antes de migrar): devolvemos los defaults.
            return new static(static::defaults());
        }
    }

    public static function flush(): void
    {
        static::$memo = null;
        Cache::forget('site_settings');
    }

    /* ---------- Accessors de conveniencia ---------- */

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    /** Link directo de WhatsApp con el número configurado. */
    public function getWhatsappLinkAttribute(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->whatsapp);

        return $digits ? "https://wa.me/{$digits}" : null;
    }

    public function getTelLinkAttribute(): ?string
    {
        $digits = preg_replace('/[^\d+]/', '', (string) $this->telefono);

        return $digits ? "tel:{$digits}" : null;
    }
}
