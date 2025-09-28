<?php

namespace App\Filament\Resources\FinancialSettlementResource\Pages;

use App\Filament\Resources\FinancialSettlementResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageFinancialSettlements extends ManageRecords
{
    protected static string $resource = FinancialSettlementResource::class;


   
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => Auth::user()->role === 'admin'),
        ];
    }
}
