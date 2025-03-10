<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    protected $fillable = ['task_id', 'user_id', 'message'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Attachments()
    {
        return $this->hasMany(Attachment::class)->where('entity_type', 'task_message');
    }
}
