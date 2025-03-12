<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipRequest extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'cv', 'date_of_birth'];
}
