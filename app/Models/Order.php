<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracking_number',
        'contact_person',
        'contact_number',
        'address',
        'map_address',
        'map_coordinates',
        'voucher_code',
        'delivery_fee',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
