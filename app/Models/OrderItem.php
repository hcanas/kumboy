<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'specifications',
        'qty',
        'price',
        'status',
        'remarks',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
