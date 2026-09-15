<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by_admin_id');
    }
}
