<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    public $timestamps = false;


    public function attendanceweekday()
    {
        return $this->belongsTo(Week_day::class);
    }

}
