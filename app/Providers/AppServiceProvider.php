<?php

namespace App\Providers;

use App\Filament\Resources\OrderResource as ResourcesOrderResource;
use App\Filament\Resources\OrderResource\Pages\ManageOrder as PagesManageOrder;
use App\Modifiers\ShippingModifier;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\OrderResource;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Shipping\ShippingPlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(function($panel){
            return $panel->plugins([
                new ShippingPlugin,
            ]);
        })
            ->register();

        LunarPanel::extensions([
            OrderResource::class => ResourcesOrderResource::class,
            OrderResource\Pages\Components\OrderItemsTable::class => ResourcesOrderResource\Pages\Components\OrderItemsTable::class
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(
            ShippingModifier::class
        );

        \Lunar\Facades\ModelManifest::replace(
            \Lunar\Models\Contracts\Product::class,
            \App\Models\Product::class,
            // \App\Models\CustomProduct::class,
        );
    }
}