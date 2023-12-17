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
        Schema::create('users', function (Blueprint $table) {
            //$table->unsignedInteger('userID')->increment()->primary();
            $table->id('userID');
            $table->string('fullname', 30);
            $table->string('email', 30)->unique();
            $table->string('password');
            
            $table->enum('userType', [-1, 0, 1, 2, 3, 4]);
            /**
             * -1: Bị khóa tài khoản
             *  0: Giám đốc
             *  1: Trưởng điểm tập kết
             *  2: Trường điểm giao dịch
             *  3: Nhân viên tập kết
             *  4: Nhân viên giao dịch
             */
            $table->string('phoneNumber', 20)->unique();
            $table->mediumInteger('belongsTo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
