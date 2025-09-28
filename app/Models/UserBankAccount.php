<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'iban',
        'account_holder_name',
        'status',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($bank_account) {
            
            // تعيين sender_id تلقائيًا
            if (auth()->check() && !$bank_account->user_id) {
                $bank_account->user_id = auth()->id();
            }

        });
    }
}
