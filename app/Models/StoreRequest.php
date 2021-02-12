<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ref_no',
        'category',
        'status',
        'evaluated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function storeApplication()
    {
        return $this->hasOne(StoreApplication::class, 'ref_no', 'ref_no');
    }

    public function storeTransfer()
    {
        return $this->hasOne(StoreTransfer::class, 'ref_no', 'ref_no');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by', 'id');
    }
}
