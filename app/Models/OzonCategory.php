<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function categoryAttributes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OzonCategoryAttribute::class);
    }
}
