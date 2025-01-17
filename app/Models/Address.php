<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\Country;
use Lunar\Models\State;

class Address extends Model
{
    protected $fillable = [
        'country',
        'state',
        'city',
        'address_line',
        'latitude',
        'longitude',
    ];




    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state');
    }
}
