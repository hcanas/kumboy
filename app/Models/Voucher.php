<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'vouchers';

    protected $fillable = [
        'store_id',
        'code',
        'amount',
        'type',
        'categories',
        'limit_per_user',
        'qty',
        'valid_from',
        'valid_to',
        'status',
    ];

    protected $casts = [
        'valid_from' => 'date:Y-m-d',
        'valid_to' => 'date:Y-m-d',
    ];

    public function getCategoriesAttribute($value)
    {
        return json_decode($value);
    }

    public function setCategoriesAttribute($value)
    {
        $this->attributes['categories'] = json_encode($value);
    }
}
