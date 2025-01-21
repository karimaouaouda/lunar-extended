<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Lunar\Models\Collection;
use Lunar\Models\Url;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(Product::all());
    }

    public function collection_products($name)
    {
        $url = Url::whereElementType((new Collection())->getMorphClass())
            ->whereDefault(true)
            ->whereSlug($name)
            ->with([
                'element.thumbnail',
                'element.products.variants.basePrices',
                'element.products.defaultUrl',
            ])->first();

        if($url->isEmpty()){
            abort(404, 'no collection with that name');
        }

        $collection = $url?->element;

        $ids = [$collection->id];
        array_push($ids, ...$collection->descendants()->get('id')->pluck('id')->toArray());

        $table = config('lunar.database.table_prefix') . 'collection_product';

        $products = \App\Models\Product::query()
            ->join($table, 'lunar_products.id', '=', "{$table}.product_id")
            ->whereIn("{$table}.collection_id", $ids)
            ->get();

        return \App\Http\Resources\ProductResource::collection($products);
    }

    public function show($product_id)
    {
        return ProductResource::make(Product::findOrFail($product_id));
    }
}
