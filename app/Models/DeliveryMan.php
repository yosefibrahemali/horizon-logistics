<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;



class DeliveryMan extends Model
{
    
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'vehicle_type',
        'vehicle_number',
        'status',
        'city',
        'user',
        'user_id',
        'uuid',
    ];

    // علاقة مع الشحنات (delivery man يقدر يكون عنده أكثر من شحنة)
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'delivery_man_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     // تعيين user_id تلقائيًا عند إنشاء DeliveryMan جديد

    protected static function booted()
    {
        static::creating(function ($deliveryMan) {
            // تعيين sender_id تلقائيًا
            if (Auth::check() && !$deliveryMan->user_id) {
                $deliveryMan->user_id = Auth::id();
            }  
                $deliveryMan->uuid = Str::uuid();
              
        });
        

    }

}
