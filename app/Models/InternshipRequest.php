<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipRequest extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'country_code', 'phone_number', 'cv', 'status'];
}
