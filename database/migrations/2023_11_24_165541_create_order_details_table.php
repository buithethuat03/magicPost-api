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
        Schema::create('order_details', function (Blueprint $table) {

            $table->uuid('orderID')->primary();

            //Information of sender and receiver
            $table->string('sender_name', 30);
            $table->string('sender_address');
            $table->string('sender_phone', 20);
            $table->string('receiver_name', 30);
            $table->string('receiver_address');
            $table->string('receiver_phone', 20);

            //Information of transaction, warehouse and timeline
            
                //Transactions and warehouses
                $table->unsignedBigInteger('first_transaction_id');
                $table->unsignedBigInteger('last_transaction_id');
                $table->unsignedBigInteger('first_warehouse_id')->nullable();
                $table->unsignedBigInteger('last_warehouse_id');

                //Timeline
                $table->json('timeline');
                //Timeline format: [{'2023-12-5 12:00:00'}, {'2023-12-5 14:00:00'}, {}, {}, ...]
            // $table->dateTime('sent_date', 0);
            // $table->dateTime('confirm_first_warehouse_come', 0)->nullable();
            // $table->dateTime('left_first_transaction', 0)->nullable();
            // $table->dateTime('come_first_warehouse', 0)->nullable();
            // $table->dateTime('left_first_warehouse', 0)->nullable();
            // $table->dateTime('come_last_warehouse', 0)->nullable();
            // $table->dateTime('left_last_warehouse', 0)->nullable();
            // $table->dateTime('come_last_transaction', 0)->nullable();
            // $table->dateTime('left_last_transaction', 0)->nullable();
            // $table->dateTime('received_date', 0)->nullable();
            // $table->dateTime('order_uncompleted', 0)->nullable();
            


            //Information of weight, shipping fee, orderType of order
            $table->double('weight', 10, 2);
            $table->double('shipping_fee', 20, 2);
            $table->enum('orderType', [0, 1]);// 0 : tai lieu, 1 : hang hoa

            //$table->enum('status', ['Đã tiếp nhận', 'Chờ tập kết 1 đến', 'Rời giao dịch 1', 'Đến tập kết 1', 'Rời tập kết 1', 'Đến tập kết 2', 'Rời tập kết 2', 'Đến giao dịch 2', 'Đang giao hàng', 'Đã giao hàng', 'Không thành công']);
            $table->string('status');
            $table->timestamps();

            //Foreign key
            $table->foreign('first_transaction_id')->references('transactionID')->on('transaction')->onDelete('cascade');
            $table->foreign('last_transaction_id')->references('transactionID')->on('transaction')->onDelete('cascade');
            $table->foreign('first_warehouse_id')->references('warehouseID')->on('warehouse')->onDelete('cascade');
            $table->foreign('last_warehouse_id')->references('warehouseID')->on('warehouse')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
