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
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط بالمستخدم
            $table->string('bank_name'); // اسم البنك
            $table->string('account_number')->unique(); // رقم الحساب
            $table->string('iban')->nullable(); // رقم IBAN
            $table->string('account_holder_name'); // اسم صاحب الحساب
            $table->enum('status', ['active', 'inactive'])->default('active'); // حالة الحساب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};
