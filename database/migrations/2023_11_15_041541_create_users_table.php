<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Kiểm tra nếu bảng 'users' đã tồn tại
        if (Schema::hasTable('users')) {
            // Nếu tồn tại, xóa bảng 'users'
            Schema::dropIfExists('users');
        }

        // Tạo bảng 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id('userID');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('fullname');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Xóa bảng 'users' nếu tồn tại
        Schema::dropIfExists('users');
    }
};