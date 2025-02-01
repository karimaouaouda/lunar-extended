<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Design extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'data',
        'preview'
    ];


    public function getPreviewUrlAttribute(): string
    {
        return Storage::disk('public')
            ->url($this->attributes['path'] . '/' . $this->attributes['preview']);
    }

    public function getPreviewPathAttribute(): string
    {
        return $this->attributes['path'] . '/' . $this->attributes['preview'];
    }

    protected $casts = [
        'data' => 'array'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function image(){
        return $this->morphOne(Image::class, 'imageable');
    }
}
