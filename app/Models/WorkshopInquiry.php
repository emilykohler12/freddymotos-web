<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopInquiry extends Model
{
    public const STATUS_NUEVA = 'nueva';
    public const STATUS_ATENDIDA = 'atendida';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'status',
    ];
}
