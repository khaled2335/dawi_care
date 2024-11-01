<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoneService extends Model
{
    use HasFactory;
    protected $table = 'done_services';
    protected $fillable = [
        'count',
        'total_cost'

    ];
}
