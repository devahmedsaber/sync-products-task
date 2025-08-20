<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'description',
        'price',
        'image_url',
        'image_path',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
