<?php

namespace App\Http\Controllers;

use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected ?string $basepath = "/designs/";

    public function store(Request $request){
        //here the Auth and authorizing Logic
        $design_name = $request->input('name');
        $design_data = $request->input('data');
        $design_image = $request->file('image');

        //generate a unique name for the image
        $image_name = Str::uuid()->toString().'.'.$design_image->getClientOriginalExtension();

        Storage::disk('public')->putFileAs($this->basepath, $design_image, $image_name);

        $design = Design::create([
            'user_id' => 1,
            'name' => $design_name,
            'data' => $design_data,
            'path' => $this->basepath,
            'preview' => $image_name,
        ]);

        $design->save();

        return response()->json([
           'message' => 'Design created successfully',
           'status_code' => 201,
            'data' => $design_image->getClientOriginalExtension()
        ]);
    }

    public function index()
    {
        return DesignResource::collection(Design::all());
    }

    public function fetch(User $user){
        return DesignResource::collection($user->designs);
    }
    public function show(Design $design){
        return DesignResource::make($design);
    }

    public function update(Design $design, Request $request)
    {
        //here the Auth and authorizing Logic
        $design_name = $request->input('name');
        $design_data = $request->input('data');
        $design_image = $request->file('image');

        $image_name = Str::uuid()->toString().'.'.$design_image->getClientOriginalExtension();

        if( Storage::disk('public')->exists($design->preview_path) ){
            Storage::disk('public')->delete($design->preview_path);
        }

        Storage::disk('public')
            ->putFileAs($this->basepath, $design_image, $design_name);

        $design->update([
            'name' => $design_name,
            'data' => $design_data,
            'path' => $this->basepath,
            'preview' => $design_image,
        ]);

        $design->save();

        return response()->json([
            'message' => 'Design updated successfully',
            'status_code' => 201,
        ]);
    }
}
