<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{



    protected $fillable = ['client_id', 'status'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
