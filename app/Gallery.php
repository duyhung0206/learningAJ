<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
	protected $table = 'galleries';
    protected $fillable = ['name', 'user_id'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->diffForHumans();
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getSingleGallery($id)
    {
        $gallery = Gallery::with('user')->where('id', $id)->first();
        $gallery->images = $this->getGalleryImageUrls($id, $gallery->id);
        return $gallery;
    }

    private function getGalleryImageUrls($id, $galleryId)
    {
        $files = DB::table('gallery_images')
            ->where('gallery_id', $id)
            ->join('files', 'files.id', '=', 'gallery_images.file_id')
            ->get();
        $finalData = [];
        foreach ($files as $key => $file) {
            $finalData[$key] = [
                'file' => $file,
                'file_id' => $file->id,
                'thumbUrl' => asset('storage/app/gallery/'.$galleryId.'/thumb/'.$file->file_name),
                'url' => asset('storage/app/gallery/'.$galleryId.'/medium/'.$file->file_name),
                'main' => asset('storage/app/gallery/'.$galleryId.'/main/'.$file->file_name),
            ];
        }
        return $finalData;
    }
}
