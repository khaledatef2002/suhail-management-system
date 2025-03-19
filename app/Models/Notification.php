<?php

namespace App\Models;

use App\Enum\NotificationEntityType;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'title', 'entity_type', 'entity_id', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayHrefAttribute()
    {
        return match($this->entity_type)
        {
            NotificationEntityType::TASK->value => route('dashboard.tasks.show', $this->entity_id),
            NotificationEntityType::TASK_MESSAGE->value => route('dashboard.tasks.show', $this->entity_id),
            NotificationEntityType::INTERNSHIP_REQUEST->value => route('dashboard.internship-requests.show', $this->entity_id)
        };
    }
}
