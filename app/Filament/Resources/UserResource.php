<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'المستخدمون';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';


     public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
    public static function getModelLabel(): string
    {
        return __('مستخدم');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('المستخدمون');
    }
    public static function getNavigationIcon(): string
    {
        $user = Auth::user();

        return $user && $user->role === 'admin'
            ? 'heroicon-o-shield-check'
            : 'heroicon-o-user';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('الإسم')
                    ->required(),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required(),
                Forms\Components\Toggle::make('role')
                    ->label('الدور')
                    ->onIcon('heroicon-o-shield-check')   // أيقونة عند اختيار "admin"
                    ->offIcon('heroicon-o-user')        // أيقونة عند اختيار "user"
                    ->onColor('success')                // لون عند اختيار "admin"
                    ->offColor('secondary')             // لون عند اختيار "user"
                    ->default(false)                    // false = user, true = admin
                    ->dehydrateStateUsing(fn($state) => $state ? 'admin' : 'user'),
                TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null) // تشفير الباسورد
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // مطلوب في الإنشاء فقط
                    ->dehydrated(fn ($state) => filled($state)), // يخزن القيمة فقط لو مش فارغ
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('تاريخ التحقق')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge(fn($state) => $state === 'admin' ? 'Admin' : 'User')
                    ->colors([
                        'success' => 'admin',
                        'secondary' => 'user',
                    ])
                    ->searchable()
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
