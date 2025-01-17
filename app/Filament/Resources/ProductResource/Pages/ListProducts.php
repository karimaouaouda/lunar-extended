<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Extending\ListPageExtension;
use Lunar\Facades\DB;
use Lunar\Models\Attribute;
use Lunar\Models\Currency;
use Lunar\Models\TaxClass;

class ListProducts extends ListPageExtension
{
    protected static string $resource = ProductResource::class;

    public function headerActions(array $actions): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false)->form(
                array_merge(
                    [Select::make('supplier_id')->relationship('supplier', 'name')->searchable()->required()],
                    \Lunar\Admin\Filament\Resources\ProductResource\Pages\ListProducts::createActionFormInputs()
                )
            )->using(
                fn (array $data, string $model) => static::createRecord($data, $model)
            )->successRedirectUrl(fn (Model $record): string => \Lunar\Admin\Filament\Resources\ProductResource::getUrl('edit', [
                'record' => $record,
            ])),
        ];
    }

    public static function createRecord(array $data, string $model): Model
    {
        $currency = Currency::getDefault();

        $nameAttribute = Attribute::whereAttributeType(
            $model::morphName()
        )
            ->whereHandle('name')
            ->first()
            ->type;

        DB::beginTransaction();
        $product = $model::create([
            'supplier_id' => $data['supplier_id'],
            'status' => 'draft',
            'product_type_id' => $data['product_type_id'],
            'attribute_data' => [
                'name' => new $nameAttribute($data['name']),
            ],
        ]);
        $variant = $product->variants()->create([
            'tax_class_id' => TaxClass::getDefault()->id,
            'sku' => $data['sku'],
        ]);
        $variant->prices()->create([
            'min_quantity' => 1,
            'currency_id' => $currency->id,
            'price' => (int) bcmul($data['base_price'], $currency->factor),
        ]);
        DB::commit();

        return $product;
    }

}
