<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{



    public $timestamps = false;

    protected $fillable = ['order_id', 'invoice_number', 'issued_at', 'total_amount'];

    protected $casts = [
        'issued_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
