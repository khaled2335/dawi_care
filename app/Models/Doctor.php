<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'name' ,
        'national_id',
        'phone_number',
        'profile_photo',
        'union_registration',
        'scientific_degree',
        'total_salary',
        'worked_days' ,
        'fixed_salary',
    ];
}
