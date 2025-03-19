<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'file_name', 'file_path', 'file_size', 'file_type', 'uploaded_by'];

    protected $appends = ['display_image'];

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

    public function getDisplayImageAttribute() : string
    {
        if (in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            return asset('storage/' . $this->file_path);
        }
        return asset('back/images/document.png');
    }

    public function getFormattedSizeAttribute() : string
    {
        return formatFileSize($this->file_size);
    }
}
