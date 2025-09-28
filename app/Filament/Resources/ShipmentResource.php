<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Filament\Resources\ShipmentResource\RelationManagers;
use App\Models\City;
use App\Models\DeliveryMan;
use App\Models\Shipment;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Support\LazyCollection;
// use App\Filament\Exports\ProductExporter;
use App\Filament\Exports\ShipmentExporter;
// use Filament\Actions\ExportAction;
// use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Actions\ExportBulkAction;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ExportAction;



class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationGroup = 'الإدارة العامة';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getModelLabel(): string
    {
        return __('شحنة');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('الشحنات');
    }

    public static function getEloquentQuery(): Builder
    {
      

        $query = parent::getEloquentQuery();

        // إذا المستخدم ليس Admin، اعرض فقط شحناته
        if (Auth::user()->role === 'user') {
            $query->where('sender_id', Auth::id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('تفاصيل الشحنة'))
                    ->description(__('تفاصيل حول الشحنة التي يتم إرسالها'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('sender_id')
                            ->default(fn () => Auth::user()->id)
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),

                        Forms\Components\TextInput::make('shipment_description')
                            ->label(__('وصف الشحنة'))
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('delivery_man_id')
                            ->label(__('السائق'))
                            ->options(function () {
                                return DeliveryMan::where('user_id', Auth::id())
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('addDeliveryMen')
                                    ->icon('heroicon-o-plus')
                                    ->tooltip('إضافة سائق جديد')
                                    ->label('إضافة سائق جديد')
                                    ->form([
                                        
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
                                    ])
                                    ->action(function (array $data, $set) {
                                        $city = DeliveryMan::create([
                                            'name'          => $data['name'],
                                            'email'         => $data['email'],
                                            'phone'         => $data['email'],
                                            'vehicle_type'  => $data['vehicle_type'],
                                            'vehicle_number'=> $data['vehicle_number'],
                                            // 'user_id' => Auth::id(),
                                        ]);

                                        // تعيين القيمة مباشرة بعد الإنشاء
                                        $set('destination_city', $city->id);
                                    })
                                ),
                        Select::make('destination_city')
                            ->label('إلى مدينة')
                            ->options(function () {
                                return City::where('user_id', Auth::id())
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state, $get) {
                                $city = City::find($state);
                               
                                if ($city) {
                                     
                                    $shippingCost = $city->shipping_cost ?? 0;
                                  
                                    $shipmentCost = $get('shipment_cost') ?? 0;
                                    $set('shipping_cost', $shippingCost);
                                    $set('total_cost', $shippingCost + $shipmentCost);
                                   // dd($shippingCost );
                                }
                            })
                            ->required()
                            ->validationMessages([
                                'required' => 'هذا الحقل مطلوب.',
                            ])
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('addCity')
                                    ->icon('heroicon-o-plus')
                                    ->tooltip('إضافة مدينة جديدة')
                                    ->form([
                                        Forms\Components\TextInput::make('name')
                                            ->label('اسم المدينة')
                                            ->required()
                                            ->maxLength(255)
                                            ->validationMessages([
                                                'required' => 'هذا الحقل مطلوب.',
                                            ]),

                                        Forms\Components\TextInput::make('shipping_cost')
                                            ->label('تكلفة الشحن')
                                            ->numeric()
                                            ->required()
                                            ->validationMessages([
                                                'required' => 'هذا الحقل مطلوب.',
                                            ]),
                                    ])
                                    ->action(function (array $data, $set) {
                                        $city = City::create([
                                            'name' => $data['name'],
                                            'shipping_cost' => $data['shipping_cost'],
                                            'user_id' => Auth::id(),
                                        ]);

                                        // تعيين القيمة مباشرة بعد الإنشاء
                                        $set('destination_city', $city->id);
                                    })
                                ), 

                        // ✅ Allowed To Open & Test
                       Grid::make()
                        ->schema([
                            Toggle::make('allowed_to_open_and_testing')
                                ->label(__('مسموح بالفتح والتجربة'))
                                ->onIcon('heroicon-o-lock-open')
                                ->offIcon('heroicon-o-lock-closed')
                                ->onColor('success')
                                ->offColor('danger'),
                            
                            Toggle::make('is_fragile')
                                ->label(__('قابل للكسر'))
                                ->onIcon('heroicon-o-cube-transparent')
                                ->offIcon('heroicon-o-cube')
                                ->onColor('warning')
                                ->offColor('secondary'),
                        ])->columns(2),

                        Forms\Components\TextInput::make('origin_city')
                            ->default('Misrata')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),
                    ]),

                Forms\Components\Section::make(__('تفاصيل المستلم'))
                    ->description('تفاصيل حول الشخص الذي يستلم الشحنة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('receiver_name')
                            ->label(__('اسم المستلم'))
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required'  => 'حقل :attribute مطلوب.',
                                'max'       => 'حقل :attribute يجب أن لا يتجاوز :max حرف.',
                             ]),
                        Forms\Components\TextInput::make('receiver_email')
                            ->label(__('البريد الإلكتروني للمستلم'))
                            ->email()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('receiver_phone')
                            ->label(__('رقم الهاتف للمستلم'))
                            ->required()
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('receiver_address')
                            ->label(__('عنوان المستلم'))
                            ->required()
                            ->maxLength(255)
                            ->default(null),
                    ]),

                Forms\Components\Section::make(__('التكلفة والحالة'))
                    ->description(__('تفاصيل حول تكلفة الشحنة وحالتها'))
                    ->columns(2)
                    ->schema([      
                        Forms\Components\Select::make('status')
                            ->label(__('الحالة'))
                            ->default('pending')
                            ->options([
                                'pending' => __('معلق'),
                                'shipment_recived' => __('تم استلام الشحنة'),
                                'on_way' => __('في الطريق'),
                                'delivered' => __('تم التوصيل'),
                                'cancelled' => __('ملغي'),
                                'returned' => __('تم الإرجاع'),
                            ])
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->label(__('تكلفة الشحن'))
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->reactive(), // هذا يضمن أن القيمة تُرسل للـ DB,

                     Forms\Components\TextInput::make('shipment_cost') 
                        ->label(__('قيمة الشحنة')) 
                        ->numeric() 
                        ->default(0) 
                        ->reactive()
                        ->afterStateUpdated(function ($set, $state, $get){ 
                            $shippingCost = $get('shipping_cost') ?? 0;
                            $shipmentCost = $state ?? 0;
                            $set('total_cost', $shippingCost + $shipmentCost);
                        })->dehydrated(),

                        Forms\Components\TextInput::make('total_cost')
                            ->label(__('التكلفة الكلية'))
                            ->numeric()
                            ->suffix('LYD')
                                    ->extraAttributes([
                                        'style' => 'background:#f0f9ff; font-weight:bold; border-radius:6px; padding:4px;'
                                    ])
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('receive_cost_from')
                            ->label(__('دفع التكلفة من'))
                            ->default('receiver')
                            ->options([
                                'sender' => __('المرسل'),
                                'receiver' => __('المستلم'),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label(__('طريقة الدفع'))
                            ->options([
                                'cash' => __('نقدًا'),
                                'local_card' => __('بطاقة محلية'),
                            ])
                            ->default('cash')
                            ->required()
                            ->searchable()
                            ->reactive(),




                        Forms\Components\Select::make('payment_status')
                            ->label(__('حالة الدفع'))
                            ->default('unpayed')
                            ->options([
                                'payed' => __('مدفوع'),
                                'unpayed' => __('غير مدفوع'),
                            ])
                            ->searchable()
                            ->required()->visible(fn () => Auth::user()->role === 'admin'),
                        Forms\Components\Select::make('financial_settlement_status')
                            ->label(__('حالة التسوية المالية'))
                            ->default('unsettled')
                            ->options([
                                'settled' => __('مسوية'),
                                'unsettled' => __('غير مسوية'),
                            ])
                            ->searchable()
                            ->required()->visible(fn () => Auth::user()->role === 'admin'),


                    ]),
            ]);
    }

   



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('sender.name')
               ->summarize(Sum::make())
                ->label(__("المرسل"))
                ->sortable()
                ->searchable(),
                
                 Tables\Columns\TextColumn::make('deliveryMan.name')
                ->label(__("السائق"))
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('shipment_description')
                    ->label(__('الوصف'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('allowed_to_open_and_testing')
                    ->label(__('مسموح بالفتح والتجربة'))
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-open')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_fragile')
                    ->label(__('قابل للكسر'))
                    ->boolean()
                    ->trueIcon('heroicon-o-cube-transparent')
                    ->falseIcon('heroicon-o-cube')
                    ->trueColor('warning')
                    ->falseColor('secondary'),    
                Tables\Columns\TextColumn::make('tracking_number')
                 ->label(__('رقم التتبع'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('origin_city')
                    ->label(__('من مدينة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('إلى مدينة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label(__('اسم المستلم'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_email')
                    ->label(__('البريد الإلكتروني للمستلم'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_phone')
                    ->label(__('رقم هاتف المستلم'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('receiver_address')
                    ->label(__('عنوان المستلم'))
                    ->searchable(),


                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->sortable()
                    ->searchable()
                   
                    ->formatStateUsing(fn ($state) => match($state) {
                       'shipment_recived' => __('تم استلام الشحنة'),
                        'pending' => __('معلق'),
                        'on_way' => __('في الطريق'),
                        'delivered' => __('تم التوصيل'),
                        'cancelled' => __('ملغي'),
                        'returned' => __('تم الإرجاع'),
                        default            => 'غير معروف',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'shipment_recived' => 'info',
                        'in_transit'       => 'warning',
                        'delivered'        => 'success',
                        default            => 'gray',
                    }),

                    TextColumn::make('payment_status')
                    ->label(__('حالة الدفع'))
                    ->sortable()
                    ->searchable()
                   
                    ->formatStateUsing(fn ($state) => match($state) {
                        'payed' => __('تم الدفع'),
                        'unpayed' => __('لم يتم الدفع'),
                        default            => 'غير معروف',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'unpayed'       => 'warning',
                        'payed'        => 'success',
                        default            => 'gray',
                    }),
                

                Tables\Columns\TextColumn::make('total_weight')
                    ->label(__('الوزن الكلي'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label(__('تكلفة الشحن'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipment_cost')
                    ->label(__('قيمة الشحنة'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label(__('التكلفة الكلية'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receive_cost_from')
                ->label(__('دفع التكلفة من'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'sender' => __('المرسل'),
                        'receiver' => __('المستلم'),
                        default => $state
                    })
                    ->badge()
                    ->colors([
                        'primary' => 'sender',
                        'success' => 'receiver',
                    ]),
               Tables\Columns\IconColumn::make('financial_settlement_status')
                    ->label(__('حالة التسوية المالية'))
                    ->getStateUsing(fn ($record) => $record->financial_settlement_status === 'settled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

   
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
                Tables\Actions\Action::make('markAsReceived')
                    ->label(__(' تعيين "تم استلام الشحنة في الشركة"'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->status = 'shipment_recived';
                        $record->save();

                        Notification::make()
                            ->title(__('تم تحديث حالة الشحنة إلى "
                            تم استلام الشحنة في الشركة"
                            سيتم إخطار سائق التوصيل بأن الشحنة جاهزة للإستلام'))
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'shipment_recived'), // يظهر فقط إذا لم تكن الشحنة مستلمة

                Tables\Actions\Action::make('pay')
                    ->label('رابط الدفع')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn ($record) => route('shipments.pay', $record->tracking_number))
                    ->openUrlInNewTab()
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->payment_method === 'local_card' && $record->payment_status === 'unpayed')
                    ,//->copyable(),

                Tables\Actions\Action::make('printLabel')
                    ->label(__('طباعة بوليصة الشحن'))
                    ->icon('heroicon-o-printer')
                    ->button() // زر عادي
                    ->color('info')
                    ->url(fn ($record) => route('shipments.print-label', $record)),
                // Quick action لتعيين Delivery Man
                Tables\Actions\Action::make('assignDeliveryMan')
                    ->label(__('تعيين سائق للتوصيل'))
                    ->icon('heroicon-o-user') // أيقونة
                    ->modalHeading('تعيين سائق لتوصيل الشحنة')
                    ->form([
                        Forms\Components\Select::make('delivery_man_id')
                            ->label(__('اختر سائق التوصيل'))
                            ->options(function () {
                                // جلب جميع رجال التوصيل
                                return DeliveryMan::where('user_id', Auth::id())
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        // تحديث الشحنة
                     
                        $record->delivery_man_id = $data['delivery_man_id'];
                        $record->save();
                        // dd($data,$record);
                       

                        Notification::make()
                            ->title(__('تم تعيين سائق التوصيل بنجاح سيتم إعلام السائق بالتفاصيل. عبر الاشعارات'))
                            ->success()
                            ->send();
                    }),
                    Tables\Actions\EditAction::make(),//->visible(fn () => Auth::user()->role === 'admin'),
                    Tables\Actions\DeleteAction::make(),//->visible(fn () => Auth::user()->role === 'admin'),
                 
           
               
      
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                     ExportBulkAction::make()
                        ->exporter(ShipmentExporter::class)
                        ->label('تصدير الشحنات')
                        
                        ->fileName(fn ($export) => "shipments-{$export->getKey()}.xlsx"),
                      
                       
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShipments::route('/'),
        ];
    }
   
}
