<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'title', 'entity_type', 'entity_id', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
