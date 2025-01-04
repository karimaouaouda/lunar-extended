<?php

namespace App\Filament\Resources\OrderResource\Pages\Components;

use Closure;
use Filament\Forms;
use Filament\Infolists\Components\Actions;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;
use Lunar\Admin\Livewire\Components\TableComponent;
use Lunar\Admin\Support\Concerns\CallsHooks;
use Lunar\Admin\Support\Extending\ViewPageExtension;
use Lunar\Admin\Support\Tables\Components\KeyValue;
use Lunar\Models\Order;
use Lunar\Models\Transaction;

/**
 * @property \Illuminate\Support\Collection $charges
 * @property \Illuminate\Support\Collection $refunds
 * @property float $availableToRefund
 * @property bool $canBeRefunded
 */
class OrderItemsTable extends ViewPageExtension
{
    use CallsHooks;


    public static function extendOrderLinesTableColumns(){

        return [
            Tables\Columns\Layout\Split::make([
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(fn () => 'data:image/svg+xml;base64, '.base64_encode(
                            Blade::render('<x-filament::icon icon="heroicon-o-photo" style="color:rgb('.Color::Gray[400].');"/>')
                        ))
                    ->grow(false)
                    ->getStateUsing(fn ($record) => $record->purchasable?->getThumbnail()?->getUrl('small')),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('description')
                                ->weight(FontWeight::Bold),
                            Tables\Columns\TextColumn::make('identifier')
                                ->color(Color::Gray),
                            Tables\Columns\TextColumn::make('options')
                                ->getStateUsing(fn ($record) => $record->purchasable?->getOptions())
                                ->badge(),
                        ]),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('unit')
                                ->alignEnd()
                                ->getStateUsing(fn ($record) => "{$record->quantity} @ {$record->sub_total->formatted}"),
                        ]),
                    ])
                        ->extraAttributes(['style' => 'align-items: start;']),
                ])
                    ->columnSpanFull(),
            ])->extraAttributes(['style' => 'align-items: start;']),
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('stock')
                        ->getStateUsing(fn ($record) => $record->purchasable?->stock)
                        ->html()
                        ->formatStateUsing(fn ($state) => __('lunarpanel::order.infolist.current_stock_level.message', [
                            'count' => $state,
                        ]))
                        ->colors(fn () => [
                            'danger' => fn ($state) => $state < 50,
                            'success' => fn ($state) => $state >= 50,
                        ]),
                    Tables\Columns\TextColumn::make('meta.stock_level')
                        ->formatStateUsing(fn ($state) => __('lunarpanel::order.infolist.purchase_stock_level.message', [
                            'count' => $state,
                        ]))
                        ->color(Color::Gray),
                    Tables\Columns\TextColumn::make('notes')
                        ->description(new HtmlString('<b>'.__('lunarpanel::order.infolist.notes.label').'</b>'), 'above'),

                    KeyValue::make('price_breakdowns')
                        ->getStateUsing(function ($record) {

                            $states = [];

                            $states['unit_price'] = "{$record->unit_price->unitFormatted(decimalPlaces: 4)}";
                            $states['quantity'] = $record->quantity;
                            $states['sub_total'] = $record->sub_total?->formatted;
                            $states['discount_total'] = $record->discount_total?->formatted;

                            foreach ($record->tax_breakdown?->amounts ?? [] as $tax) {
                                $states[$tax->description] = $tax->price->formatted;
                            }

                            $states['total'] = $record->total?->formatted;

                            return $states;
                        }),

                    TextColumn::make('attachments')
                        ->getStateUsing(fn ($record) => 'manage order line attachments')
                        ->openUrlInNewTab()
                        ->alignEnd()
                        ->url(fn ($record) => route('filament.lunar.resources.orders.attachments', ['record' => $record->order->id, 'line' => $record->id]))
                        ->extraAttributes([
                            'class' => 'mt-4 text-white',
                            'style' => 'width:fit-content;padding:0.125rem 0.5rem;background:dodgerblue;color:white!important;border-radius:5px;'
                        ])

                    ]),
            ])
                ->collapsed()
                ->collapsible(),
        ];
    }

}
