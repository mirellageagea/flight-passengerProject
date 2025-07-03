<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Flight extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    // protected $fillable = [
    //     'number',
    //     'departure_city',
    //     'arrival_city',
    //     'departure_time',
    //     'arrival_time',
    // ];

    protected $guarded = [];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function passengers()
    {
        //return $this->hasMany(Passenger::class);
        return $this->belongsToMany(Passenger::class, 'flight_passenger', 'flight_id', 'passenger_id');
    }

    public function user()
    {
        return auth()->user();
    }
}
