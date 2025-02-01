<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['user_id'];




    public function user(){
        return $this->belongsTo(User::class);
    }

    public function images(){
        return $this->morphMany(Image::class, 'imageable');
    }
}
