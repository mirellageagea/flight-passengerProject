<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

class PassengerImageController extends Controller
{
    public function upload(Request $request, Passenger $passenger)
    {
        try {
            // Validate the uploaded image
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
            ]);

            // Generate unique filename
            $image = $request->file('image');
            $filename = uniqid('passenger_', true) . '.' . $image->getClientOriginalExtension();

            // Save original image to local disk (public)
            $image->storeAs('passenger_images', $filename, 'public');
            Log::info('Original image saved', ['filename' => $filename]);

            // Create a thumbnail image (resized)
            Log::info('Generating thumbnail...');
            $thumbnail = Image::make($image->getRealPath())
                ->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent blurry upscaling
                })
                ->encode('jpg', 75); // Save as JPG with lower quality

            Log::info('Thumbnail created successfully');

            // Upload thumbnail to S3/MinIO
            Storage::disk('s3')->put("passenger_thumbnails/{$filename}", $thumbnail);
            Log::info('Thumbnail uploaded to S3/MinIO', ['filename' => $filename]);

            // Update passenger record in DB
            $passenger->update(['image' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Image and thumbnail uploaded successfully.',
                'passenger' => $passenger->fresh(),
            ], 201);
            
        } catch (\Throwable $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Image upload failed. Check logs for details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
