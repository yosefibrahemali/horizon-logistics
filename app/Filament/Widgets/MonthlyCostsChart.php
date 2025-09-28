<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use \App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class MonthlyCostsChart extends ChartWidget
{
    protected static ?string $heading = 'التكلفة الشهرية للشحنات';

    protected function getData(): array
    {
        $data = [];
        $months = range(1, 12);

        foreach ($months as $month) {
        $data['labels'][] = Carbon::create(null, $month, 1)->locale('ar')->translatedFormat('F');   

            $query = Shipment::query()->whereMonth('created_at', $month);

            // إذا كان المستخدم ليس admin
            if (Auth::user()->role !== 'admin') {
                $query->where('sender_id', Auth::id());
            }

            $data['datasets'][0]['data'][] = $query->sum('total_cost');
        }

        // إعداد Dataset للعرض في Chart.js أو أي مكتبة أخرى
        $data['datasets'][0]['label'] = __('إجمالي التكلفة الشهرية');
        $data['datasets'][0]['backgroundColor'] = '#3b82f6'; // أزرق
        $data['datasets'][0]['borderColor'] = '#2563eb';
        $data['datasets'][0]['borderWidth'] = 1;
        

        return $data;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
