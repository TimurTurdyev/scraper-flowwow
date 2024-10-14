<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function ozonCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OzonCategory::class);
    }
}
