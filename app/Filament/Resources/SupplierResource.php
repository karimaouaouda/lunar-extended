<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Address;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lunar\Models\Country;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getDefaultFormComponents(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->email()
                ->prefixIcon('heroicon-o-envelope')
                ->required()
                ->unique('suppliers', 'email'),

            Forms\Components\TextInput::make('website_url')
                ->prefixIcon('heroicon-o-link')
                ->url()
                ->required(),

            Forms\Components\Select::make('country_code')
                ->options(function (){
                    $codes = Country::all()->pluck('name', 'phonecode');
                    $codes = $codes->map(function($item, $key){
                        if (str_starts_with($key, '+')){
                            return "{$item} ({$key})";
                        }
                       return "{$item} (+{$key})";
                    });

                    return $codes;
                }),
            Forms\Components\TextInput::make('phone')
                ->tel()
                ->required(),

            Forms\Components\TextInput::make('note')
                ->required(),

            Forms\Components\FileUpload::make('attachment')
                ->disk('public')
                ->directory('suppliers/attachments')
                ->required()
                ->maxSize(2048),

        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getDefaultFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->prefix('#'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->badge()
                    ->color(Color::Green)
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->address?->country()->first()->name ?? 'no address'),
                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->address?->state()->first()->name ?? 'no address'),
                Tables\Columns\TextColumn::make('website_url')
                    ->extraAttributes([
                        'class' => 'text-sm'
                    ])
                    ->url(fn ($state) => $state)
                    ->icon('heroicon-o-link')
                    ->extraAttributes([
                        'class' => 'max-w-xs truncate',
                        'style' => 'overflow:hidden;text-overflow;ellipsis;'
                    ]),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->multiple()
                    ->options(fn() => Country::all()->pluck('name', 'id'))
                    ->modifyQueryUsing(function(Builder $query, $state){
                        if (empty($state['values'])) return $query;
                        return $query->whereHas('address', fn($query) => $query->whereIn('country', $state['values']));
                    }),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('__')
                        ->icon('heroicon-o-circle-stack')
                        ->label('Products')
                        ->url(fn ($record): string => static::getUrl('products', ['record'=>$record->id]))
                        ->color('primary'),
                    Tables\Actions\Action::make('___')
                        ->icon('heroicon-o-map-pin')
                        ->label('Address')
                        ->color(Color::Amber)
                        ->form([
                            Forms\Components\Hidden::make('supplier_id')
                                ->default(fn($record) => $record->id),
                            Forms\Components\Select::make('country')
                                ->default(fn($record) => $record->address?->country->id ?? null)
                                ->live()
                                ->searchable()
                                ->options(fn() => Country::all()->pluck('name', 'id')),
                            Forms\Components\Select::make('state')
                                ->default(fn($record) => $record->address?->state->id ?? null)
                                ->searchable()
                                ->options(fn(Forms\Get $get) => Country::find($get('country'))?->states->pluck('name', 'id') ?? []),
                            Forms\Components\TextInput::make('address_line')
                                ->default(fn($record) => $record->address?->address_line ?? null),
                        ])->action(function(array $data, $record){
                            $data['latitude'] = $data['longitude'] = 1.2;
                            $data['city'] = $data['state'];
                            $record->address()->updateOrCreate([
                                'supplier_id' => $record->id
                            ], $data);
                        })
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
            'products' => Pages\ManageProducts::route('/{record}/products'),
        ];
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    //static function

    public static function getAddressFormComponents() : array
    {
        return [
            Forms\Components\Select::make('country')
                ->relationship('country', 'name')
                ->searchable(),
            Forms\Components\Select::make('state')
                ->relationship('state', 'name')
                ->searchable(),
        ];
    }
}
