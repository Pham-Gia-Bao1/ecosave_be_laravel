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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 255);
            $table->string('avatar', 255);
            $table->string('logo', 255);
            $table->string('store_type', 100);
            $table->string('opening_hours', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'closed']);
            $table->string('contact_email', 255)->nullable();
            $table->string('contact_phone', 15)->nullable();
            $table->string('address', 255)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->text(column: 'soft_description')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
