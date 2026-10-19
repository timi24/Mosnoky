<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'reference', 'amount', 'payment_method', 'status', 'paid_at',
        'customer_phone', 'customer_reference', 'submitted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** Le client a déclaré son paiement et attend la vérification de l'équipe. */
    public function isAwaitingVerification(): bool
    {
        return $this->status === 'EN_ATTENTE' && $this->submitted_at !== null;
    }
}
