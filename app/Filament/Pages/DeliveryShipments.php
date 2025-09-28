<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DeliveryShipments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.delivery-shipments';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
