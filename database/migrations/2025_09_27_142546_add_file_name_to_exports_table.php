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
        Schema::table('exports', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('exporter');
        });
    }

    public function down(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            $table->dropColumn('file_name');
        });
    }
};
