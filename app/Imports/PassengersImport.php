<?php

namespace App\Imports;

use App\Models\Passenger;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PassengersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Passenger([
            'flight_id' => $row['flight_id'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'password' => bcrypt($row['password']), // or leave empty if not imported
            'dob' => $row['dob'],
            'passport_expiry_date' => $row['passport_expiry_date'],
        ]);
    }
}
