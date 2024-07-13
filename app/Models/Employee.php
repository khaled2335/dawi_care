<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $timestamps = false;
    use HasFactory;


    public function Emlpoyee_week_day()
    {
        return $this->hasMany(Emlpoyee_week_day::class);
    }
}
