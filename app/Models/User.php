<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'date_of_birth',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['full_name', 'display_image'];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function created_tasks()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }
    public function assigned_tasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }
    public function messages()
    {
        return $this->hasMany(TaskMessage::class);
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unseen_notifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function getDisplayImageAttribute() : string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('back/images/users/default-avatar-icon-of-social-media-user-vector.jpg');
    }
}
