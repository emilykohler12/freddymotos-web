<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordResetCode extends Model
{
    protected $fillable = [
        'email',
        'code_hash',
        'attempts',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public const MAX_ATTEMPTS = 5;
    public const CODE_TTL_MINUTES = 10;

    /** Genera un código de 6 dígitos, lo guarda (hasheado) para el email dado y devuelve el código en texto plano. */
    public static function generateFor(string $email): string
    {
        static::where('email', $email)->delete();

        $code = (string) random_int(100000, 999999);

        static::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
        ]);

        return $code;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasAttemptsLeft(): bool
    {
        return $this->attempts < self::MAX_ATTEMPTS;
    }
}
