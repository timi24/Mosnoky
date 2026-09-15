<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{



    protected $fillable = [
        'order_id', 'tracking_number', 'carrier', 'shipped_at',
        'expected_delivery_at', 'delivered_at', 'status', 'tracking_comment',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'expected_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
