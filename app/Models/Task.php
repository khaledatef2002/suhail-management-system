<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'status', 'due_date', 'assignee_id', 'creator_id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function messages()
    {
        return $this->hasMany(TaskMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class)->where('entity_type', 'task');
    }
}
