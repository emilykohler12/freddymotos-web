<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Configuración del sitio. Siempre una sola fila (id = 1).
 * Se lee con SiteSetting::current() (memoizado solo por request, sin cache
 * persistente entre procesos: es una tabla de una fila, no hace falta y
 * evita quedarse con datos viejos después de guardar en el panel).
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

    /**
     * Valores por defecto mientras el admin no cargó nada en Configuración.
     * Sin datos de contacto/redes inventados: todo lo que no sea de sistema
     * queda en null para que el Home no muestre nada hasta que el admin lo cargue.
     */
    public static function defaults(): array
    {
        return [
            'nombre_local' => 'Freddy Motos',
            'telefono' => null,
            'whatsapp' => null,
            'email' => null,
            'direccion' => null,
            'horario_atencion' => null,
            'historia' => null,
            'fecha_creacion' => null,
            'instagram_url' => null,
            'facebook_url' => null,
            'logo_path' => null,
            'hero_photo_path' => null,
            'moneda' => 'ARS',
            'tax_rate' => null,
            'payment_methods' => [],
        ];
    }

    protected function casts(): array
    {
        return [
            'payment_methods' => 'array',
            'tax_rate' => 'decimal:2',
            'fecha_creacion' => 'date',
        ];
    }

    public static function current(): self
    {
        return static::$memo ??= static::resolve();
    }

    private static function resolve(): self
    {
        try {
            return static::query()->first() ?: new static(static::defaults());
        } catch (\Throwable) {
            // La tabla todavía no existe (antes de migrar): devolvemos los defaults.
            return new static(static::defaults());
        }
    }

    public static function flush(): void
    {
        static::$memo = null;
    }

    /* ---------- Accessors de conveniencia ---------- */

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    /** Foto real que se muestra en el Hero del Home, o null para el placeholder ilustrado. */
    public function getHeroPhotoUrlAttribute(): ?string
    {
        return $this->hero_photo_path ? Storage::disk('public')->url($this->hero_photo_path) : null;
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

    /** Link a Google Maps con la dirección cargada por el admin, o null si no la cargó. */
    public function getMapsUrlAttribute(): ?string
    {
        return $this->direccion
            ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($this->direccion)
            : null;
    }

    /** Años desde la fecha de creación cargada por el admin, o null si no la cargó. */
    public function getAnosTrayectoriaAttribute(): ?int
    {
        return $this->fecha_creacion ? (int) $this->fecha_creacion->diffInYears(now()) : null;
    }
}
