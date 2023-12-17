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
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id('warehouseID');
            $table->string('warehouse_name');
            $table->string('warehouse_address');
            $table->string('warehouse_phone', 20)->unique();
            $table->unsignedBigInteger('warehouse_manager_id')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_manager_id')->references('userID')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
