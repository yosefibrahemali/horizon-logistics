<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'shipment_description',
        'tracking_number',
        'origin_city',
        'destination_city',
        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'receiver_address',
        'status',
        'total_weight',
        'shipping_cost',
        'shipment_cost',
        'total_cost',
        'receive_cost_from',
        'payment_method',
        'payment_status',
        'is_fragile',
        'allowed_to_open_and_testing',
        'financial_settlement_status',
        'delivery_man_id'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }
    protected static function booted()
    {
        static::creating(function ($shipment) {
            
            
            // تعيين sender_id تلقائيًا
            if (Auth::check() && !$shipment->sender_id) {
                $shipment->sender_id = Auth::user()->id;
            }

            if($shipment->status === "delivered"){
                $shipment->payment_status = "payed";
            }
            // توليد tracking_number إذا لم يكن موجود
            if (!$shipment->tracking_number) {
                $shipment->tracking_number = 'TRK-' . strtoupper(uniqid());
            }
        });
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'destination_city');
    }


      public function financialSettlement()
    {
        return $this->belongsTo(FinancialSettlement::class, 'shipment_id');
    }

}
