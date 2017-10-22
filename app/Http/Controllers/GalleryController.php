<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Files;
use App\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Gallery::where('user_id', Auth::user()->id)->with('user')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
        ]);
        if ($validator->fails()) {
            return response($validator->errors()->all(), 422);
        }
        $gallery = Gallery::create([
            'name' => $request->input('name'),
            'user_id' => Auth::user()->id,
        ]);
        return response($gallery, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $galleryObj = new Gallery;
        return $galleryObj->getSingleGallery($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function uploadImage(Request $request)
    {
        //check if the file exist
        if(!$request->hasFile('file')){
            return response('No file sent', 400);
        }

        //check if the file valid
        if(!$request->file('file')->isValid()){
            return response('File is not valid', 400);
        }

        //validating
        $validator = Validator::make($request->all(),[
            'galleryId' => 'required|integer',
            'file' => 'required|mimes:jpeg,jpg,png|max:6000'
        ]);

        if($validator->fails()){
            return response('There are errors in the form', 400);
        }

        $fileObject = new Files();
        return $fileObject->uploadThumbAndMainImage($request);

    }

    public function deleteSingleImage(Request $request)
    {
        $imageId = $request->input('id');
        try {
            DB::beginTransaction();
            // delete the file from the files table
            $file = Files::findOrFail($imageId);
            $file->delete();
            // remove the entry from the gallery image pivot table
            DB::table('gallery_images')->where('file_id', $file->id)->delete();
            // delete the actual image from local
            $fileName = explode('/', $file->file_path);
            $fileName = $fileName[count($fileName) - 1];
            $mainImage = "gallery/{$request->input('galleryId')}/main/" . $fileName;
            $thumbImage = "gallery/{$request->input('galleryId')}/thumb/" . $fileName;
            $mediummage = "gallery/{$request->input('galleryId')}/medium/" . $fileName;
            $local = Storage::disk('local');
           
            if($local->has($mainImage)){
                $local->delete($mainImage);
            }
            if($local->has($thumbImage)){
                $local->delete($thumbImage);
            }
            if($local->has($mediummage)){
                $local->delete($mediummage);
            }
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
        }
        return response($this->show($request->input('galleryId')), 200);
    }
}
