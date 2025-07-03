<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class PassengerImageController extends Controller
{
    public function upload(Request $request, Passenger $passenger)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max 2 MB
        ]);

        $image = $request->file('image');
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

        // Save original to local
        $image->storeAs('public/passenger_images', $filename);

        // Save thumbnail to S3
        $thumbnail = Image::make($image)->resize(100, 100)->encode();
        Storage::disk('s3')->put('passenger_thumbnails/' . $filename, $thumbnail);

        // Update passenger record
        $passenger->update(['image' => $filename]);

        return response(['success' => true,
         'message' => 'Image uploaded successfully']);
    }
}
