<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week_day extends Model
{
    public $timestamps = false;
    use HasFactory; 
    protected $fillable = ['day', 'date', 'doctor_id'];
}
