<?php

namespace App\Filament\Resources\UserBankAccountResource\Pages;

use App\Filament\Resources\UserBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageUserBankAccounts extends ManageRecords
{
    protected static string $resource = UserBankAccountResource::class;
    // protected static ?int $navigationSort = 4;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => Auth::user()->bankAccounts->count() < 1),
        ];
    }
}
