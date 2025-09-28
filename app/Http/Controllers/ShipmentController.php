<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS1D;
use App\Models\Shipment;
use Milon\Barcode\Facades\DNS1DFacade;

class ShipmentController extends Controller
{

    public function printLabel(Shipment $shipment)
    {
        $barcode = DNS1DFacade::getBarcodeHTML($shipment->tracking_number, 'C128'); 
        return view('shipments.print-label', compact('shipment', 'barcode'));
    }

    public function pay($tracking_number)
    {
        // dd($tracking_number);
        $shipment = Shipment::where('tracking_number', $tracking_number)->firstOrFail();    

        return view('shipments.online-pay', compact('shipment'));
        
       
    }

}
