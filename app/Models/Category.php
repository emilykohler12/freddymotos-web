<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'image_path',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            $category->slug = $category->slug ?: Str::slug($category->name);
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** URL de la imagen subida por el admin, o null para mostrar el ícono genérico. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
