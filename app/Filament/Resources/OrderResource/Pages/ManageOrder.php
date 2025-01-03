<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource as ResourcesOrderResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\OrderResource;
use Lunar\Admin\Filament\Resources\OrderResource\Concerns\DisplaysTransactions;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder as PagesManageOrder;
use Lunar\Admin\Support\Concerns\CallsHooks;
use Lunar\Admin\Support\Extending\ViewPageExtension;
use Lunar\Admin\Support\Infolists\Components\Livewire;

class ManageOrder extends ViewPageExtension
{
    use CallsHooks;
    use OrderResource\Concerns\DisplaysShippingInfo;
    use OrderResource\Concerns\DisplaysOrderTimeline;
    use OrderResource\Concerns\DisplaysOrderTotals;
    use DisplaysTransactions;
    protected static string $resource = ResourcesOrderResource::class;


    public static function getOrderLinesTable(): Livewire
    {
        return Livewire::make('lines')
            ->content(ResourcesOrderResource\Pages\Components\OrderItemsTable::class);
    }

    public function extendOrderLinesTableColumns() : array
    {
        return [];
    }

    public function extendsInfolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ...$infolist->getComponents(true)
        ]);
    }
}
