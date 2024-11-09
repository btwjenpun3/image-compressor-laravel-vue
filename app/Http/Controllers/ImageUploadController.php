<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Intervention\Image\Laravel\Facades\Image as Gambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\PngEncoder;

class ImageUploadController extends Controller
{
    public function index()
    {
        return inertia('Home/Index', [
            'recaptcha_key' => env('GOOGLE_RECAPTCHA_KEY')
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'images'            => 'required|array|max:5',
            'images.*'          => 'required|file|mimes:jpeg,jpg|max:10512',
            'recaptcha_token'   => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $recaptchaToken = $request->input('recaptcha_token');

            $secretKey = env('GOOGLE_RECAPTCHA_SECRET');

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaToken}");
            $responseData = json_decode($response);
            
            if ($responseData->success !== true || $responseData->score <= 0.5) {
                throw new \Exception("Are you a robot ?");
            } 

            $time   = now();
            $year   = $time->format('Y');
            $month  = $time->format('m');
            $day    = $time->format('d');

            $images = $request->file('images');

            $imagePath = [];

            foreach ($images as $image) {
                $originalName   = $image->getClientOriginalName();
                $path           = $image->store("images/{$year}/{$month}/{$day}", 'public');
                
                $encodeName = str_replace("images/{$year}/{$month}/{$day}", '', $path);
                $compress = $this->compressImage($image, $path, "compressed/{$year}/{$month}/{$day}/{$encodeName}");

                $compressData = json_decode($compress->getContent(), true);

                if ($compressData['status'] === 500) {
                    throw new \Exception('Terjadi kesalahan, harap di coba kembali');
                }

                $imagePath[] = [
                    'name'              => $originalName,
                    'original_path'     => $path,
                    'original_size'     => $image->getSize(),
                    'compressed_path'   => $compressData['compressed_image'],
                    'compressed_size'   => $compressData['compressed_size']
                ];
            }

            Image::insert($imagePath);

            DB::commit();

            return Inertia::render('Home/Index', [
                'images' => $imagePath
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    function compressImage($image, $sourcePath, $destinationPath) {
        try {
            $image = ImageManager::gd()->read(storage_path('app/public/' . $sourcePath));
            
            $fullDestinationPath = storage_path('app/public/' . $destinationPath);

            $destinationDir = dirname($fullDestinationPath); 

            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true); 
            }
            $image = $image->encodeByMediaType(quality: 50);

            $imageSize = $image->size();

            $image->save($fullDestinationPath);            

            return response()->json([
                'status'            => 200,
                'original_image'    => env('APP_URL') . '/' . $sourcePath,
                'compressed_image'  => env('APP_URL') . '/' . $destinationPath,
                'compressed_size'   => $imageSize
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
