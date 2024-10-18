<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Streetlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'location',
        'score',
        'notes',
        'request_details',
        'energy_source',
        'description',
        'crime_level',
        'ward',
        'land_uses'
      ];

}
