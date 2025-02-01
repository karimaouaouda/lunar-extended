<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{

    protected ?string $album_base_path = 'albums/';
    
    public function fetchAlbum($albumId)
    {
        $album = Album::find($albumId);
        $albumresource = AlbumResource::make($album);

        return $albumresource;
    }

    // puch a picture inside this album
    public function pushPicture(Request $request, $albumId)
    {
        //check if there is a user connected using api
       /*  if(! Auth::check()){
            return response()->json(['error' => 'Unauthorized'], 401);
        } */

        $album = Album::find($albumId);

        $image = $request->file('image');

        //check if the user is the owner of this album
       /*  if($album->user_id != Auth::id()){
            return response()->json(['error' => 'Unauthorized'], 401);
        } */

        Storage::disk('public')->putFileAs($this->album_base_path . $albumId, $image, $image->getClientOriginalName());

        $album->images()->create([
            'path' => $this->album_base_path . $albumId . '/' . $image->getClientOriginalName(),
            'name' => $image->getClientOriginalName(),
        ]);

        return response()->json(['message' => 'Image uploaded successfully']);

    }
}
