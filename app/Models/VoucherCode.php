<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCode extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'voucher_codes';

    protected $fillable = [
        'code',
        'type',
        'value',
        'affected_categories',
        'affected_products',
        'valid_until',
    ];

    protected $casts = [
        'valid_until' => 'date:Y-m-d',
    ];
}
