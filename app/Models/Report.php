<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{



    protected $fillable = ['client_id', 'order_id', 'review_id', 'report_type', 'description', 'status'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
