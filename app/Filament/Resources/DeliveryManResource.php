<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryManResource\Pages;
use App\Filament\Resources\DeliveryManResource\RelationManagers;
use App\Models\DeliveryMan;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class DeliveryManResource extends Resource
{
    protected static ?string $model = DeliveryMan::class;

    protected static ?string $navigationGroup = 'الإدارة العامة';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getModelLabel(): string
    {
        return __('سائق');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('السائقين');
    }


     public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // إذا المستخدم ليس Admin، اعرض فقط شحناته
        if (Auth::user()->role === 'user') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('الإسم'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                     ->label(__('البريد الإلكتروني'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(__('رقم الهاتف'))
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('vehicle_type')
                    ->label(__('نوع المركبة'))
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('vehicle_number')
                    ->label(__('رقم المركبة'))
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->label(__('الحالة'))
                    ->required()
                    ->options([
                        'active' => __('نشط'),
                        'inactive' => __('غير نشط'),
                    ])
                    ->default('active')
                    ->searchable(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('الإسم'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('البريد الإلكتروني'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('رقم الهاتف'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label(__('نوع المركبة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_number')
                    ->label(__('رقم المركبة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => match($state) {
                       'active' => __('نشط'),
                        'inactive' => __('غير نشط'),
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'inactive'       => 'warning',
                        'active'        => 'success',
                         default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('المستخدم'))
                    ->searchable()->visible(Auth::user()->role === 'admin'),
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
                Tables\Columns\TextColumn::make('uuid')
                    ->label('رابط واجهة عامل التوصيل')
                   // ->value('ede')
                    ->getStateUsing(fn ($record) => route('deliverymen.show', $record->uuid))
                    ->copyable()
                    ->copyMessage('تم نسخ الرابط إلى الحافظة')
                    ->copyMessageDuration(1500)
                    ->toggleable(false), // اختياري: لا تخفيه في واجهة الأعمدة
                        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('viewDeliveryMan')
                ->label('عرض واجهة السائق')
                ->url(fn ($record) => route('deliverymen.show',['uuid'=>$record->uuid] ))
                ->icon('heroicon-o-user')
                ->color('info')
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
            'index' => Pages\ManageDeliveryMen::route('/'),
        ];
    }
}
