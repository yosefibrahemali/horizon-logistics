<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ShipmentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات حالة الشحنات';

    protected function getData(): array
    {
        $statuses = [
            'pending' => 'معلق',
            'shipment_recived' => 'تم استلام الشحنة',
            'on_way' => 'في الطريق',
            'delivered' => 'تم التوصيل',
            'cancelled' => 'ملغي',
            'returned' => 'تم الإرجاع',
        ];

        $query = Auth::user()->role === 'admin'
            ? Shipment::query() // admin يرى كل الشحنات
            : Shipment::where('sender_id', Auth::id()); // user يرى شحناته فقط

        $data = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'عدد الشحنات',
                    'data' => [],
                    'backgroundColor' => [
                        '#f59e0b', // pending - أصفر
                        '#10b981', // shipment_recived - أخضر فاتح
                        '#3b82f6', // on_way - أزرق
                        '#16a34a', // delivered - أخضر داكن
                        '#ef4444', // cancelled - أحمر
                        '#6b7280', // returned - رمادي
                    ],
                ],
            ],
        ];

        foreach ($statuses as $key => $label) {
            $data['labels'][] = $label;
            $data['datasets'][0]['data'][] = $query->where('status', $key)->count();
        }

        return $data;
    }


    // نوع الرسم البياني
    protected function getType(): string
    {
        return 'pie'; // يمكن تغييره إلى 'bar' أو 'line'
    }
}
