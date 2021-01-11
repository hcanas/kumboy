<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $fillable = [
        'user_id',
        'name',
        'contact_number',
        'address',
        'map_coordinates',
        'map_address',
        'open_until',
        'logo',
    ];

    protected $casts = [
        'open_until' => 'datetime:Y-m-d',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
