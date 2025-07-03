<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Casts\HashPassword;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Passenger extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Notifiable, Auditable;

    protected $fillable = [
        'first_name',
        'last_name',
        'flight_id',
        'email',
        'password',
        'dob',
        'passport_expiry_date',
        'image',

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

    public function user()
    {
        return auth()->user();
    }
}
