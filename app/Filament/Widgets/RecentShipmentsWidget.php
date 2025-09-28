<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;


class RecentShipmentsWidget extends BaseWidget
{
    protected static ?string $heading = 'أحدث الشحنات';
    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        // تحديد الاستعلام حسب الدور
        $query = Auth::user()->role === 'admin'
            ? Shipment::query()                  // admin يرى كل الشحنات
            : Shipment::where('sender_id', Auth::id()); // user يرى شحناته فقط

        return $table
            ->query($query->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('رقم التتبع')
                    ->sortable(),

                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('اسم المستلم'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
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
                            'on_way' => 'info',
                            'pending'       => 'warning',
                            'delivered'        => 'success',
                            'cancelled'        => 'danger',
                            'returned'        => 'danger',
                            default            => 'gray',
                        }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d M Y H:i'),
            ])->emptyStateHeading('لا يوجد أي شحنات');
    }
   // public function get

}