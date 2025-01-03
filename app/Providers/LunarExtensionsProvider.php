<?php

namespace App\Providers;

use App\Filament\Resources\AttachementResource;
use App\Filament\Resources\OrderResource as ResourcesOrderResource;
use App\Filament\Resources\OrderResource\Pages\ManageOrder as PagesManageOrder;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\OrderResource;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder;
class LunarExtensionsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /* LunarPanel::panel(function($panel){
            return $panel
                ->resources([
                    AttachementResource::class,
                ]);
        })->register(); */


    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
