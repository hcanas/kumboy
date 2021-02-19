<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'filename',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
