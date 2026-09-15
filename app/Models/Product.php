<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{



    protected $fillable = [
        'category_id', 'created_by_admin_id', 'name', 'slug',
        'description', 'price', 'stock', 'alert_threshold', 'status',
        'available_sizes', 'available_colors',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available_sizes' => 'array',
        'available_colors' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Administrator::class, 'created_by_admin_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
