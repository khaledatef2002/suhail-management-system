<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'file_name', 'file_path', 'file_size', 'file_type', 'uploaded_by'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'entity_id')->where('entity_type', 'task');
    }

    public function taskMessage()
    {
        return $this->belongsTo(TaskMessage::class, 'entity_id')->where('entity_type', 'task_message');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
