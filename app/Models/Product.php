<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $fillable = [
        'store_id',
        'name',
        'qty',
        'price',
        'main_category',
        'sub_category',
        'sold',
        'preview',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
