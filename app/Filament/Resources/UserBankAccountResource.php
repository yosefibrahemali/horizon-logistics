<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBankAccountResource\Pages;
use App\Filament\Resources\UserBankAccountResource\RelationManagers;
use App\Models\UserBankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserBankAccountResource extends Resource
{

    protected static ?string $model = UserBankAccount::class;

    protected static ?string $navigationGroup = 'الحسابات المالية';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';


     public static function getModelLabel(): string
    {
        return __('حساب بنكي');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('الحسابات البنكية');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              
                Forms\Components\TextInput::make('bank_name')
                    ->label(__('اسم البنك'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_number')
                    ->label(__('رقم الحساب'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('iban')
                    ->label(__('رقم IBAN'))
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('account_holder_name')
                    ->label(__('اسم صاحب الحساب'))
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('status')
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('user_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label(__('اسم البنك'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->label(__('رقم الحساب'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('iban')
                    ->label(__('رقم IBAN'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_holder_name')
                    ->label(__('اسم صاحب الحساب'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإنشاء'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('تاريخ التعديل'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageUserBankAccounts::route('/'),
        ];
    }
}
