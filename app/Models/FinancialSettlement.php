<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSettlement extends Model
{
    use HasFactory;

    protected $casts = [
        'shipments' => 'array', // هذا يحوّل JSON إلى مصفوفة تلقائيًا عند القراءة والكتابة
    ];

    protected $fillable = [
        'shipment_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_date',
    ];

    
   public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }



    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    // protected static function booted()
    // {
    //     static::created(function ($finance_sett) {
    //         // جلب الشحنات المختارة من Form
    //         $shipmentIds = request('shipment_ids'); // أو $finance_sett->shipment_ids إذا تم تمريرها
    //         if ($shipmentIds && count($shipmentIds)) {
    //             $finance_sett->shipments()->sync($shipmentIds); // ربط الشحنات بالتسوية

    //             // تعيين user_id تلقائيًا من أول شحنة
    //             $firstShipment = Shipment::find($shipmentIds[0]);
    //             if ($firstShipment && !$finance_sett->user_id) {
    //                 $finance_sett->user_id = $firstShipment->sender_id;
    //                 $finance_sett->save();
    //             }
    //         }
    //     });
    // }


}
