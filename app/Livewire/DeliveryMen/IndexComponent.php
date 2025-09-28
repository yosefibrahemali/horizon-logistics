<?php

namespace App\Livewire\DeliveryMen;

use Livewire\Component;
use App\Models\DeliveryMan;
use App\Models\Shipment;


class IndexComponent extends Component
{
    public function render()
    {
        return view('livewire.delivery-men.index-component');
    }
    public $deliveryMan;
    public $shipments = [];
    public $currentShipment;

    public $trackingNumber; // For adding shipment

    
    public function mount()
    {
        $this->deliveryMan = DeliveryMan::firstOrFail(); // أو findByUuid
        $this->shipments = $this->deliveryMan->shipments()->orderBy('created_at', 'desc')->get();
        $this->currentShipment = $this->shipments->firstWhere('status',['on_way','shipment_recived']) ?? $this->shipments->first();
    }

    public function setCurrent($shipmentId)
    {
        $this->currentShipment = Shipment::find($shipmentId);
        // $this->dispatchBrowserEvent('notify', ['message' => "تم تعيين الشحنة {$shipmentId} كشحنة حالية.", 'type' => 'success']);
    }

    public function updateStatus($shipmentId, $status)
    {
        $shipment = Shipment::find($shipmentId);
        //  dd($shipment);
        if ($shipment) {
            $shipment->status = $status;
            $shipment->save();

            // تحديث القوائم
            $this->shipments = $this->deliveryMan->shipments()->orderBy('created_at', 'desc')->get();
            if ($this->currentShipment && $this->currentShipment->id == $shipmentId) {
                $this->currentShipment = $shipment;
            }

            session()->flash('success', "تم تحديث حالة الشحنة {$shipmentId} إلى {$status}.");
        }
    }

    public function addShipmentByTracking()
    {
        $tracking = $this->trackingNumber;
        if (!$tracking) {
            session()->flash('error', 'الرجاء إدخال رقم التتبع.');
            return;
        }

        // جلب الشحنة من قاعدة البيانات
        $shipment = Shipment::where('tracking_number', $tracking)->first();

        if ($shipment) {
            // تعيين الشحنة لسائق التوصيل الحالي
            $shipment->delivery_man_id = $this->deliveryMan->id;
            $shipment->save();

            // تعيينها كشحنة حالية مباشرة
            $this->currentShipment = $shipment;

            // تحديث القوائم
            $this->shipments = $this->deliveryMan->shipments()->get();
            
            session()->flash('success', "تم إضافة الشحنة بنجاح");
        } else {
            session()->flash('error', "لا توجد شحنة بهذا الرقم.");
        }

        $this->trackingNumber = null; // تنظيف الحقل
    }


    
}
