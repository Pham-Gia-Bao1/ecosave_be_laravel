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
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('username'); // Thay 'name' thành 'username'
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true); // Trạng thái kích hoạt (mặc định là true)
            $table->string('avatar')->nullable(); // Hình đại diện
            $table->string('address')->nullable(); // Địa chỉ
            $table->unsignedBigInteger('role')->default(2); // Vai trò (mặc định là 2)
            $table->string('phone_number')->nullable(); // Số điện thoại
            $table->decimal('latitude', 10, 8)->nullable(); // Vĩ độ
            $table->decimal('longitude', 11, 8)->nullable(); // Kinh độ
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
        Schema::dropIfExists('user');
    }
};
