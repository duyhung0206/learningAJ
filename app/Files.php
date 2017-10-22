<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
class Files extends Model
{
    protected $fillable = ['file_name', 'mime_type', 'file_size', 'file_path', 'status', 'type'];

    public function uploadThumbAndMainImage(Request $request)
    {
    	$galleryId = $request->input('galleryId');
        $file = $request->file('file');

		$mimeType = $file->getClientMimeType();
        $fileSize = $file->getClientSize();
        $extension = $file->guessExtension();
        $fileName = uniqid() . '.' . $extension;

        $image = Image::make($file);

        // generate the thumb and medium image
        // $imageThumb = Image::make($file)->fit(320)->crop(320, 240, 0, 0);
        $imageThumb = Image::make($file)->fit(320)->resize(320, 320, function ($constraint) {
            $constraint->aspectRatio();
        });
        $imageThumb->encode($extension);

        $imageMedium = Image::make($file)->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $imageMedium->encode($extension);

        $image->encode($extension);

        $local = Storage::disk('local');
        $result = $local->put('gallery/'.$galleryId.'/main/'.$fileName, (string) $image, 'public');
        $local->put('gallery/'.$galleryId.'/medium/'.$fileName, (string) $imageMedium, 'public');
        $local->put('gallery/'.$galleryId.'/thumb/'.$fileName, (string) $imageThumb, 'public');
        if($result){
            $file = Files::create([
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'file_path' => asset('storage/app/gallery/'.$galleryId.'/main/'.$fileName),
                'type'      => 'local'
            ]);

            DB::table('gallery_images')->insert([
                'gallery_id' => $galleryId,
                'file_id' => $file->id
            ]);

            $file_img = Files::find($file->id);
            $file_img->status = 1;
            $file_img->save();
        }
        return [
                'file' => $file_img,
                'file_id' => $file_img->id,
                'url' => $file_img->file_path,
                'thumbUrl' => asset('storage/app/gallery/'.$galleryId.'/thumb/'.$fileName),
                'url' => asset('storage/app/gallery/'.$galleryId.'/medium/'.$fileName),
                'main' => asset('storage/app/gallery/'.$galleryId.'/main/'.$fileName),

        ];
    }
}
