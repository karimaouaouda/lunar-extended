<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Address;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Get;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Country;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->form([
                    Wizard::make([
                        Wizard\Step::make('Supplier Information')
                            ->schema(SupplierResource::getDefaultFormComponents()),
                        Wizard\Step::make('Address Information')
                            ->schema(static::getAddressComponents())
                    ]),
                ])
                ->modalSubmitActionLabel('Create Supplier')
                ->action(function (array $data){
                    $data['phone'] = $data['country_code'] . $data['phone'];
                    static::createSupplier($data);
                }),
        ];
    }


    public static function createSupplier(array $data){
        DB::transaction(function () use ($data){
           $supplier = Supplier::create([
               'name' => $data['name'],
               'phone' => $data['phone'],
               'email' => $data['email'],
               'website_url' => $data['website_url'],
               'note' => $data['note'],
               'attachment' => $data['attachment'],
           ]);

           $supplier->save();

           $supplier->address()->create([
               'country' => $data['country'],
               'state' => $data['state'],
               'city' => $data['state'],
               'address_line' => $data['address_line'],
               'latitude' => 1.2,
               'longitude' => 1.2,
           ]);

           return $supplier;
        });
    }

    public static function getAddressComponents(): array
    {
        return [
            Select::make('country')
                ->options(fn() => Country::all()->pluck('name', 'id'))
                ->live()
                ->required()
                ->searchable(),
            Select::make('state')
                ->options(function(Get $get){
                    return Country::find($get('country'))?->states()->pluck('name', 'id') ?? [];
                })
                ->searchable()
                ->required(),
            TextInput::make('address_line')
                ->required(),
        ];
    }
}
