<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'user_activities';

    protected $fillable = [
        'user_id',
        'date_recorded',
        'action_taken',
    ];

    protected $casts = [
        'date_recorded' => 'datetime:Y-m-d H:i:s',
    ];
}
