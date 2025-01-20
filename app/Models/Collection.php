<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Collection as LunarCollection;

class Collection extends LunarCollection
{
    



    public function getAllProductsAttribute(){
        return null;
    }
}
