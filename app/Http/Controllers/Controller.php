<?php

namespace App\Http\Controllers;

use App\Models\Font;
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

        $font = new Font();

        $font->user_id = 1;
        $font->name = $name;
        $font->name_ext = $name . '.' .$font_file->getClientOriginalExtension();
        $font->path = $font->base_path . 'user_1/';
        
        Storage::disk('public')->putFileAs($font->path,$font_file, $font->name_ext);

        $font->generateCssFile();

        dd($font);
    }
}
