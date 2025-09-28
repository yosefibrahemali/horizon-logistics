<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSettlementShipment extends Model
{
    protected $table = 'financial_settlement_shipment';
    
    protected $fillable = [
        'financial_settlement_id',
        'shipment_id',
    ];
}
