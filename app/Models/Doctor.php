<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Week_day;
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
     // Define the worked_days attribute as a cast to an array if stored as JSON
     protected $casts = [
        'worked_days' => 'string',
    ];
    public function weekDays()
    {
        return $this->hasMany(Week_day::class);
    }
    public function clinic()
{
    return $this->belongsTo(Clinic::class, 'clinic_id');
}

}

