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
        'user_id',
        'contact_person',
        'contact_number',
        'label',
        'address',
        'map_coordinates',
        'map_address',
    ];
}
