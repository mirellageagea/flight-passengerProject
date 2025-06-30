<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'number',
    //     'departure_city',
    //     'arrival_city',
    //     'departure_time',
    //     'arrival_time',
    // ];

    protected $guarded = [];

    public function passengers()
    {
        //return $this->hasMany(Passenger::class);
        return $this->belongsToMany(Passenger::class, 'flight_passenger', 'flight_id', 'passenger_id');
    }
}
