<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductResource extends ResourceExtension
{
    public function extendForm(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            ...$form->getComponents(withHidden: true),

            \Filament\Forms\Components\TextInput::make('custom_column'),
            TextInput::make('supplier_id')
        ]);
    }

    public function extendTable(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table->columns([
            TextColumn::make('supplier_id')
                ->label('Supplier')
                ->searchable()
                ->getStateUsing(fn($record) => $record->supplier->name),
            ...$table->getColumns(),
            \Filament\Tables\Columns\TextColumn::make('id')
                ->getStateUsing(fn($record) => $record->id)
        ]);
    }
}
