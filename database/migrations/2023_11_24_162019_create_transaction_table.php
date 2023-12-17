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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id('transactionID');
            $table->string('transaction_name');
            $table->string('transaction_address');
            $table->string('transaction_phone', 20)->unique();
            //$table->uuid('transaction_manager_id')->nullable();
            $table->unsignedBigInteger('transaction_manager_id')->nullable();
            $table->unsignedBigInteger('belongsTo');
            $table->timestamps();

            $table->foreign('transaction_manager_id')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('belongsTo')->references('warehouseID')->on('warehouse')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
