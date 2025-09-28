<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shipping_cost',
        'user_id'
    ];

    protected static function booted()
    {
        static::creating(function ($city) {
            // تعيين sender_id تلقائيًا
            if (auth()->check() && !$city->user_id) {
                $city->user_id = auth()->id();
            }

          
        });
    }
    public function shipments()
    {
        return $this->hasMany(Shipment::class,'destination_city');
    }
}
