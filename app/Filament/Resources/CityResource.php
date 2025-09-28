<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use Filament\Notifications\Notification;
use Filament\Notifications\Notification;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

   // protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'الإدارة العامة';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';


    public static function getModelLabel(): string
    {
        return __('مدينة');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('المدن');
    }
   
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // إذا المستخدم ليس Admin، اعرض فقط المدن التي أضافها
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
                    ->label(__('الاسم'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_cost')
                    ->label(__('تكلفة الشحن'))
                    ->required()
                    ->numeric(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('الاسم'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label(__('تكلفة الشحن'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record, $action) {
                        if ($record->shipments()->count() > 0) {
                            $action->cancel();

                             Notification::make()
                                ->title('لا يمكن حذف المدينة مباشرة')
                                ->body('المدينة تحتوي على شحنات مرتبطة. استخدم زر "حذف مع الشحنات".')
                                ->danger()
                                ->send();
                              

                            return;
                        }
                    }),

                Tables\Actions\Action::make('deleteWithRelations')
                    ->label('حذف مع الشحنات')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الحذف')
                    ->modalSubheading('سيتم حذف جميع الشحنات المرتبطة بهذه المدينة أيضاً، هل أنت متأكد؟')
                    ->modalButton('نعم، احذف الكل')
                    ->action(function ($record) {
                        $record->shipments()->delete();
                        $record->delete();
                    }),

                
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
            'index' => Pages\ManageCities::route('/'),
        ];
    }
}
