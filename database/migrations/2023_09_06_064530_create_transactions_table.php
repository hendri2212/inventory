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
            $table->text('information');
            $table->binary('image');
            $table->biginteger('price');
            $table->date('date');
            $table->unsignedBigInteger('borrow_id');
            $table->unsignedBigInteger('condition_id');
            $table->unsignedBigInteger('good_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('room_id');
            $table->timestamps();

            $table->foreign('borrow_id')->references('id')->on('borrows');
            $table->foreign('condition_id')->references('id')->on('conditions');
            $table->foreign('good_id')->references('id')->on('goods');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
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
