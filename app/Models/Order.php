<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{



    protected $fillable = [
        'client_id', 'order_number', 'subtotal', 'shipping_fee',
        'total', 'status', 'shipping_address_snapshot',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
