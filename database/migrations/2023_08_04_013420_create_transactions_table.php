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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->text('infomation');
            $table->binary('image');
            $table->biginteger('price');
            $table->date('date');
            $table->enum('condition', ['sangat_baik', 'kurang_baik', 'rusak']);
            $table->unsignedBigInteger('goods_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('room_id');
            $table->timestamps();

            $table->foreign('goods_id')->references('id')->on('goods');
            $table->foreign('shop_id')->references('id')->on('supliers');
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
