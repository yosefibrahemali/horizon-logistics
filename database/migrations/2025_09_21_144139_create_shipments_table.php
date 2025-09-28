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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();
            
            $table->foreignId('destination_city')->constrained('cities');
            $table->foreignId('region_id')->nullable()->constrained('regions');

            $table->foreignId('delivery_man_id')
                ->nullable()
                ->constrained('delivery_men')
                ->nullOnDelete();
   
            $table->string('shipment_description')->nullable(); 
            $table->string('tracking_number');
            $table->string('origin_city')->default('misrata'); 
            
            $table->string('receiver_name'); 
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_address')->nullable();
            $table->enum('delivery_type',['to_cus','in office'])
                ->default('cash');   
            
            $table->enum('status',['pending','on_way','delivered','cancelled','returned','shipment_recived'])
                ->default('pending');
            $table->enum('payment_method',['cash','local_card'])
                ->default('cash');    

            $table->enum('payment_status', ['payed','unpayed'])
                ->default('unpayed');
            
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->decimal('shipment_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->boolean('is_fragile')->default(false);
            $table->boolean('allowed_to_open_and_testing')->default(false);

            $table->enum('financial_settlement_status', ['settled', 'unsettled'])->default('unsettled');

            $table->enum('receive_cost_from', ['sender', 'receiver']);
            
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
