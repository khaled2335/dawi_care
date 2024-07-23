<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;

class Week_day extends Model
{
    public $timestamps = false;
    use HasFactory; 
    protected $fillable = ['day', 'date', 'doctor_id'];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function attendanceofweekday()
    {
        return $this->hasMany(Attendance::class , 'day_id');
    }

}
