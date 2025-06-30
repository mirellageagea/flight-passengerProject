<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Casts\HashPassword;

class Passenger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'flight_id',
        'email',
        'password',
        'dob',
        'passport_expiry_date',

    ];

     //protected $guarded = [];

    protected $casts = [
        'password' => HashPassword::class,
    ];

    public function flights()
    {
        //return $this->belongsTo(Flight::class);
        return $this->belongsToMany(Flight::class, 'flight_passenger', 'passenger_id', 'flight_id');
    }
}
