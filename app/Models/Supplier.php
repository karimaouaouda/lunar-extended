<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'website_url',
        'email',
        'phone',
        'attachment',
        'note'
    ];


    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }



    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
