<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use \App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShipmentsChart extends ChartWidget
{
    protected static ?string $heading = 'عدد الشحنات شهرياً';

    protected function getData(): array
    {
        $data = [];
        $months = range(1, 12);

        foreach ($months as $month) {
         //   $data['labels'][] = date("M", mktime(0, 0, 0, $month, 1));
            $data['labels'][] = Carbon::create(null, $month, 1)->locale('ar')->translatedFormat('F');   


            // كل شهر يبدأ باستعلام جديد
            $query = Auth::user()->role === 'admin' 
                ? Shipment::query()
                : Shipment::where('sender_id', Auth::id());

            $data['datasets'][0]['data'][] = $query->whereMonth('created_at', $month)->count();
        }

        // إعداد Dataset
        $data['datasets'][0]['label'] = __('عدد الشحنات');
        $data['datasets'][0]['backgroundColor'] = '#3b82f6'; // أزرق
        $data['datasets'][0]['borderColor'] = '#2563eb';
        $data['datasets'][0]['borderWidth'] = 1;

        return $data;
    }



    protected function getType(): string
    {
        return 'bar';
    }
}
