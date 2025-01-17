<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Forms\Components\TranslatedTextInput;
use Lunar\Admin\Support\Tables\Columns\TranslatedTextColumn;

class ManageProducts extends ManageRelatedRecords
{
    protected static string $resource = SupplierResource::class;

    protected static string $relationship = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Products';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TranslatedTextInput::make('attribute_data.name')
                    ->label('product name'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attribute_data.name')
            ->columns([
                ...ProductResource::getTableColumns()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->form([
                        Forms\Components\Hidden::make('supplier_id')
                            ->default($this->record->id),
                        ...ProductResource\Pages\ListProducts::createActionFormInputs()
                    ])
                    ->using(fn(array $data, string $model) => ListProducts::createRecord($data, $model))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => ProductResource\Pages\EditProduct::getUrl(compact('record'))),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
