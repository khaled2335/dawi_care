<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Week_day;
class Employee extends Model
{
    public $timestamps = false;
    use HasFactory;


    public function weekdays()
    {
        return $this->hasMany(Week_day::class, 'emplyee_id');

    }
  
}
