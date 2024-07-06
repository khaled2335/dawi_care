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
        'worked_days' ,
        'fixed_salary',
    ];
     // Define the worked_days attribute as a cast to an array if stored as JSON
     protected $casts = [
        'worked_days' => 'string',
    ];

}

