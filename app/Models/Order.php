<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    // Estado del PEDIDO (armado / logística).
    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_ENVIADO = 'enviado';
    public const STATUS_ENTREGADO = 'entregado';
    public const STATUS_PAGADO = 'pagado'; // legacy, se mantiene por compatibilidad con pedidos viejos
    public const STATUS_CANCELADO = 'cancelado';

    // Estado del PAGO.
    public const PAYMENT_STATUS_PENDIENTE = 'pendiente';
    public const PAYMENT_STATUS_PAGADO = 'pagado';
    public const PAYMENT_STATUS_RECHAZADO = 'rechazado';

    public const DELIVERY_RETIRO = 'retiro';
    public const DELIVERY_ENVIO = 'envio';

    public const PAYMENT_MERCADOPAGO = 'mercadopago';
    public const PAYMENT_WHATSAPP = 'whatsapp';

    // Métodos de pago reales (los carga el admin cuando marca un pedido coordinado por WhatsApp como pagado).
    public const PAYMENT_EFECTIVO = 'efectivo';
    public const PAYMENT_TRANSFERENCIA = 'transferencia';
    public const PAYMENT_TARJETA_DEBITO = 'tarjeta_debito';
    public const PAYMENT_TARJETA_CREDITO = 'tarjeta_credito';

    public const REAL_PAYMENT_METHODS = [
        self::PAYMENT_EFECTIVO => 'Efectivo',
        self::PAYMENT_TRANSFERENCIA => 'Transferencia',
        self::PAYMENT_TARJETA_DEBITO => 'Tarjeta débito',
        self::PAYMENT_TARJETA_CREDITO => 'Tarjeta crédito',
    ];

    // De dónde vino el pedido: se mantiene fijo aunque después cambie el payment_method real.
    public const ORIGIN_WEB = 'web';
    public const ORIGIN_WHATSAPP = 'whatsapp';

    protected $fillable = [
        'uuid',
        'customer_id',
        'status',
        'payment_status',
        'delivery_method',
        'payment_method',
        'origin',
        'subtotal',
        'discount',
        'shipping_cost',
        'shipping_zone_id',
        'total',
        'shipping_address',
        'notes',
        'mp_preference_id',
        'mp_payment_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function markPaid(?string $paymentId = null): void
    {
        $this->forceFill([
            'payment_status' => self::PAYMENT_STATUS_PAGADO,
            'mp_payment_id' => $paymentId ?: $this->mp_payment_id,
            'paid_at' => now(),
        ])->save();

        ActivityLog::log('pedido', "Pedido #{$this->id} pagado ({$this->formatted_total})", $this);
    }

    public function isWhatsappOrigin(): bool
    {
        return $this->origin === self::ORIGIN_WHATSAPP;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAGADO || $this->status === self::STATUS_PAGADO;
    }

    /** Pedidos efectivamente pagos (incluye el status "pagado" legacy). */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('payment_status', self::PAYMENT_STATUS_PAGADO)
            ->orWhere('status', self::STATUS_PAGADO));
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$ ' . number_format((float) $this->total, 0, ',', '.');
    }
}
