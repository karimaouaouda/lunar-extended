<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attribute_data' => $this->attribute_data,
            'images' => MediaResource::collection($this->images),
            'brand' => BrandResource::make($this->brand),
            'prices' => PriceResource::collection($this->prices),
            'product_type' => ProductTypeResource::make($this->productType),
            'options' => ProductOptionResource::collection($this->productOptions),
            'variants' => ProductVariantResource::make($this->variants),
            'supplier' => SupplierResource::make($this->supplier),
        ];
    }
}
