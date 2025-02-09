<?php

namespace App\Http\Controllers;

use App\Http\Resources\FontResource;
use App\Models\Font;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function createFont(Request $request){
        $font_file = $request->file('font');

        $name = $request->input('name');
        $fontStyle = $request->input('font_style', 'normal');
        $fontWeight = $request->input('font_weight', 'normal');
       
        $font = new Font();
        $font->user_id = 1;

        $font->path = "/fonts/user_" . $font->user_id . "/" . $name . "/";

        $font->font_style = $fontStyle;
        $font->font_weight = $fontWeight;

        $font->name = $name;

        $font->filename = "{$name}-{$fontWeight}-{$fontStyle}". '.' .$font_file->getClientOriginalExtension();
        
        Storage::disk('public')->putFileAs($font->path,$font_file, $font->filename);

        $font->generateCssFile();

        $font->save();

        return response()->json([
            'message' => 'font saved successfuully'
        ]);
    }

    public function fetchFonts(User $user){
        return FontResource::collection($user->fonts()->where('font_weight', 'normal')->where('font_style', 'normal')->get());
    }
}
