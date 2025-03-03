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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->integer('discount_percent');
            $table->string('product_type');
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('rating', 2, 1)->default(0); // Thêm cột rating
            $table->softDeletes();
            $table->timestamps();
            $table->string('origin')->nullable(); // Xuất xứ
            $table->text('ingredients')->nullable(); // Thành phần
            $table->text('usage_instructions')->nullable(); // Hướng dẫn sử dụng
            $table->text('storage_instructions')->nullable(); // Bảo quản
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
