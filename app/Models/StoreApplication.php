<?php

namespace App\Models;

use App\Traits\Model\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreApplication extends Model
{
    use HasFactory, WithoutTimestamps;

    protected $table = 'store_application_requests';

    protected $fillable = [
        'store_id',
        'ref_no',
        'name',
        'contact_number',
        'address_line',
        'map_coordinates',
        'map_address',
        'open_until',
        'attachment',
    ];

    protected $casts = [
        'open_until' => 'datetime:Y-m-d',
    ];

    public function storeRequest()
    {
        return $this->belongsTo(StoreRequest::class);
    }
}
