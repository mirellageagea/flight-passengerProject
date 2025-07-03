<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PassengersImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportPassengerController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PassengersImport, $request->file('file'));

        return response(['success' => 'true, Passengers imported successfully']);
    }
}
