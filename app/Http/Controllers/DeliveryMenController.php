<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMan;
use Illuminate\Http\Request;

class DeliveryMenController extends Controller
{
    public function show($uuid)
    {
        // جلب بيانات السائق
        $deliveryMan = DeliveryMan::where('uuid', $uuid)->firstOrFail();

        // جلب الشحنات المرتبطة به
        $shipments = $deliveryMan->shipments;

        // اختيار الشحنة الحالية: أول شحنة جاهزة للتوصيل
        $currentShipment = $shipments->firstWhere('status', ['on_way','shipment_recived']);

        // إذا لم توجد شحنة جاهزة، يمكن اختيار أي شحنة أخرى
        if (!$currentShipment && $shipments->count() > 0) {
            $currentShipment = $shipments->first();
        }

        return view('delivery-man.index', compact('shipments', 'deliveryMan', 'currentShipment'));
    }

}
