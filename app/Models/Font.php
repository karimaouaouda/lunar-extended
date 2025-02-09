<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Font extends Model
{
    public ?string $base_path = "fonts/";
    protected $fillable = ['user_id', 'name', "name_ext", 'path', 'css_url'];

    public function getFontUrlAttribute(){
        return asset('storage/' . $this->base_path . "user_{$this->user->id}/{$this->name}/{$this->filename}");
    }

    public function generateCssFile(){

        $url = $this->getFontUrlAttribute();
        $path = "/fonts/user_" . $this->user_id . "/";

        $template = "@font-face {
    font-family: '{$this->name}';
    font-weight : '{$this->font_weight}';
    font-style: '{$this->font_style}';
    src: url({$url}) format('embedded-opentype'), /* Internet Explorer */
         url({$url}) format('woff2'),             /* Super Modern Browsers */
         url({$url}) format('woff'),              /* Pretty Modern Browsers */
         url({$url}) format('truetype'),          /* Safari, Android, iOS */
         url({$url}) format('svg');               /* Legacy iOS */
}";

        $filename = "fonts.css";

        Storage::disk('public')->append(
            $path . $filename,
            $template,
        );

        $this->css_url = asset('storage/' . $path . $filename );
    }


    public function user(){
        return $this->belongsTo(User::class);
    }
}
