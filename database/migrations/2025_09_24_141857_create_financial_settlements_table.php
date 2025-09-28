<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_settlements', function (Blueprint $table) {
            $table->id();
             $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade'); // ربط بالجدول shipments

            $table->decimal('total_amount', 10, 2); // إجمالي قيمة الشحنة
            $table->decimal('paid_amount', 10, 2)->default(0); // المبلغ المدفوع حتى الآن
            $table->decimal('remaining_amount', 10, 2)->default(0); // المبلغ المتبقي
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending'); // حالة الدفع
            $table->date('payment_date')->nullable(); // تاريخ آخر دفعة أو الدفع الكامل
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_settlements');
    }
};
