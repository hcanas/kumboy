<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $fillable = [
        'email',
        'code',
        'created_at',
        'expires_at',
        'status',
    ];
}
