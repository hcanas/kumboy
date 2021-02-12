<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddressBook extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'user_address_book';

    protected $fillable = [
        'label',
        'user_id',
        'contact_person',
        'contact_number',
        'address_line',
        'map_coordinates',
        'map_address',
    ];
}
