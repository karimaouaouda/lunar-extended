<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Product as LunarProduct;

class Product extends LunarProduct
{

    protected $fillable = [
        'supplier_id',
        'attribute_data',
        'product_type_id',
        'status',
        'brand_id',
    ];
    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
