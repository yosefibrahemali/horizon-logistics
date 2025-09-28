<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialSettlementResource\Pages;
use App\Filament\Resources\FinancialSettlementResource\RelationManagers;
use App\Models\FinancialSettlement;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FinancialSettlementResource extends Resource
{
    protected static ?string $model = FinancialSettlement::class;

    protected static ?string $navigationGroup = 'الحسابات المالية';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';


   public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check() && Auth::user()->role === 'user') {
            // جلب التسويات الخاصة بالشحنات التي يملكها المستخدم
            $query->whereHas('shipment', function ($q) {
                $q->where('sender_id', Auth::id());
            });
        }

        return $query;
    }





    public static function getModelLabel(): string
    {
        return __('تسوية مالية');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('التسويات المالية');
    }



    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['shipment_id'])) {
            $shipment = \App\Models\Shipment::find($data['shipment_id']);
            if ($shipment) {
                $total = $shipment->shipment_cost + ($shipment->shipping_cost ?? 0);
                $discounted = round($total * 0.98, 2); // خصم 2%

                $data['total_amount'] = $total;
                $data['paid_amount'] = $discounted;
                $data['remaining_amount'] = 0;
            }
        }
        return $data;
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الشحنة')
                    ->schema([
                        Forms\Components\Select::make('shipment_id')
                            ->label('اختر الشحنة')
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->options(function ($get) {
                                $userId = $get('user_id');
                                $query = \App\Models\Shipment::with('sender');
                                if ($userId) {
                                    $query->where('sender_id', $userId);
                                }
                                return $query->get()->mapWithKeys(function ($shipment) {
                                    return [
                                        $shipment->id => $shipment->shipment_description . ' - ' . ($shipment->sender->name ?? '')
                                    ];
                                });
                            })->columnSpanFull()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $shipment = \App\Models\Shipment::find($state);
                                    if ($shipment) {
                                        $total = $shipment->shipment_cost + ($shipment->shipping_cost ?? 0);
                                        $discounted = round($total * 0.96, 2); // خصم 2%

                                        $set('total_amount', $total);
                                        $set('paid_amount', $discounted);
                                        $set('remaining_amount', 0);

                                    }
                                }
                            }),

                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم')
                            ->reactive()
                            ->required()
                            ->default(fn ($get) => ($state = $get('shipment_id')) ? optional(\App\Models\Shipment::find($state))->sender_id : null)
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->hidden(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('التفاصيل المالية')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('الإجمالي')
                            ->required()
                            ->numeric()
                            ->dehydrated(),// يمكن تركها للعرض فقط

                     Forms\Components\TextInput::make('paid_amount')
                        ->label('المدفوع')
                        ->numeric()
                        ->required()
                        ->reactive() // مهم للتحديث المباشر
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $total = $get('total_amount') ?? 0;   // قيمة الإجمالي الحالية
                            $remaining = $total - $state;
                            $set('remaining_amount', $remaining >= 0 ? $remaining : 0);
                        }),


                        Forms\Components\TextInput::make('remaining_amount')
                            ->label('المتبقي')
                            ->required()
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('حالة الدفع')
                            ->required()
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'partial' => 'مدفوع جزئيًا',
                                'paid' => 'مدفوع بالكامل',
                            ])
                            ->searchable(),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label('تاريخ الدفع')->columnSpanFull(),
                    ])
                    ->columns(2),

              
                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shipment.tracking_number')
                    ->label('الشحنات')
                   
                    ->sortable(false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('المدفوع')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('المتبقي')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('حالة الدفع')
                     ->badge(fn ($state) => match($state) {
                        'pending' => 'قيد الانتظار',
                        'partial' => 'مدفوع جزئيًا',
                        'paid' => 'مدفوع بالكامل',
                     })
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'partial',
                        'success' => 'paid',
                    ])
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(Auth::user()->role === 'admin'),
                Tables\Actions\DeleteAction::make()->visible(Auth::user()->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->visible(Auth::user()->role === 'admin'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFinancialSettlements::route('/'),
        ];
    }
}
