<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;


class ShipmentOverview extends BaseWidget
{
    protected function getCards(): array
    {
        // تحقق من الدور
        $query = Auth::user()->role === 'admin'
            ? Shipment::query()                 // admin يرى كل الشحنات
            : Shipment::where('sender_id', Auth::id()); // user يرى شحناته فقط

        return [
            Card::make('إجمالي الشحنات', $query->count())
                ->description('عدد كل الشحنات')
                ->icon('heroicon-o-truck'),

            Card::make('الشحنات المعلقة', $query->where('status', 'pending')->count())
                ->description('في انتظار المعالجة')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Card::make('الشحنات المسلمة', $query->where('status', 'delivered')->count())
                ->description('تم توصيلها بنجاح')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Card::make('الإجمالي المالي', $query->sum('total_cost') . ' LYD')
                ->description('إجمالي التكلفة الكلية')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }

}
