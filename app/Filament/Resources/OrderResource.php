<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ManageAttachments;
use Lunar\Admin\Support\Extending\ResourceExtension;

class OrderResource extends ResourceExtension
{
    public function extendPages(array $pages) : array
    {
       return [
            ...$pages,
            'attachments' => ManageAttachments::route('/{record}/{line}/attachments'),
        ];
    }
}
