<?php

namespace App\Livewire\Shipments;

use App\Models\Shipment;
use Livewire\Component;
use Filament\Notifications\Notification;


class OnlinePayComponent extends Component
{
    public $shipment;
    public $moamalat = false;
    protected $listeners = ['paymentCompleted'];

    public function paymentCompleted($shipmentId, $paymentData = [])
    {
        
      //  dd($shipmentId,$paymentData);
        

        $this->emit('shipmentUpdated', $shipmentId); // لو تحب تحدث UI
    }

    public function mount($shipment)
    {
        $this->shipment = $shipment;
        // dd($this->shipment);
    }
    public function render()
    {
        return view('livewire.shipments.online-pay-component');
    }

    public function pay($payment_methd)
    {
        $this->moamalat = true;
        // dd($payment_methd);
        
    }

}
