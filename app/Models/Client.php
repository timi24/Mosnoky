<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'client_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'client_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'client_id');
    }
}
