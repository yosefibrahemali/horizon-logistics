<x-filament-panels::page>
    <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-800">📦 شحنات المندوب</h2>

        @if($shipments->isEmpty())
            <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                لا توجد شحنات حالياً.
            </div>
        @else
            <div class="space-y-3">
                @foreach($shipments as $shipment)
                    <div class="p-4 bg-white shadow rounded-lg border">
                        <p><span class="font-bold">رقم التتبع:</span> {{ $shipment->tracking_number }}</p>
                        <p><span class="font-bold">العنوان:</span> {{ $shipment->city->name ?? 'غير محدد' }}</p>
                        <p><span class="font-bold">المبلغ:</span> {{ number_format($shipment->total_cost, 2) }} د.ل</p>
                        <p><span class="font-bold">الحالة:</span> {{ $shipment->status }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
