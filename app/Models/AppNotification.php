<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{

    protected $table = 'notifications';



    public $timestamps = false;

    protected $fillable = ['user_id', 'title', 'content', 'is_read', 'sent_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
