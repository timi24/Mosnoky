<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{



    protected $fillable = ['client_id', 'product_id', 'order_id', 'rating', 'comment', 'moderation_status'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
