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
    // 'id' va en la lista porque firstOrNew(['id' => 1]) y updateOrCreate lo asignan en masa.
    protected $fillable = [
        'id',
        'nombre_local',
        'telefono',
        'whatsapp',
        'email',
        'direccion',
        'horario_atencion',
        'dias_atencion',
        'horarios',
        'historia',
        'fecha_creacion',
        'instagram_url',
        'facebook_url',
        'logo_path',
        'hero_photo_path',
        'moneda',
        'tax_rate',
        'payment_methods',
    ];

    public const DIAS = [
        'lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue',
        'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom',
    ];

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
            'dias_atencion' => [],
            'horarios' => null,
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
            'dias_atencion' => 'array',
            'horarios' => 'array',
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

    /** Días de atención seleccionados, ya traducidos a etiquetas cortas (Lun, Mar, ...). */
    public function getDiasAtencionLabelsAttribute(): array
    {
        return collect($this->dias_atencion ?? [])
            ->map(fn ($dia) => self::DIAS[$dia] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Líneas listas para mostrar ("Lun a Vie: 09:00 a 18:00"). Los días seguidos con el mismo
     * horario se agrupan; los días cerrados no aparecen. Mientras el admin no cargue `horarios`
     * se usan los campos viejos (días + texto libre) para no dejar el sitio sin datos.
     */
    public function getHorarioLineasAttribute(): array
    {
        if ($this->horarios === null) {
            $legacy = array_filter([
                implode(' · ', $this->dias_atencion_labels),
                $this->horario_atencion,
            ]);

            return $legacy ? [implode(': ', $legacy)] : [];
        }

        $grupos = [];
        foreach (self::DIAS as $clave => $etiqueta) {
            $dia = $this->horarios[$clave] ?? [];
            $turnos = array_filter([
                ! empty($dia['manana']) ? $dia['manana']['desde'] . ' a ' . $dia['manana']['hasta'] : null,
                ! empty($dia['tarde']) ? $dia['tarde']['desde'] . ' a ' . $dia['tarde']['hasta'] : null,
            ]);
            if (! $turnos) {
                $grupos[] = null;

                continue;
            }

            $texto = implode(' y ', $turnos);
            $ultimo = array_key_last($grupos);
            if ($ultimo !== null && $grupos[$ultimo] !== null && $grupos[$ultimo]['texto'] === $texto) {
                $grupos[$ultimo]['fin'] = $etiqueta;
            } else {
                $grupos[] = ['inicio' => $etiqueta, 'fin' => $etiqueta, 'texto' => $texto];
            }
        }

        return collect($grupos)->filter()
            ->map(fn ($g) => ($g['inicio'] === $g['fin'] ? $g['inicio'] : $g['inicio'] . ' a ' . $g['fin']) . ': ' . $g['texto'])
            ->values()
            ->all();
    }

    /** Años desde la fecha de creación cargada por el admin, o null si no la cargó. */
    public function getAnosTrayectoriaAttribute(): ?int
    {
        return $this->fecha_creacion ? (int) $this->fecha_creacion->diffInYears(now()) : null;
    }
}
