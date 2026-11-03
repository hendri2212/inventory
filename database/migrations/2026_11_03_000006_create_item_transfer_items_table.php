<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('item_transfer_id');
            $table->foreignId('item_unit_id');
            $table->unique(['item_transfer_id', 'item_unit_id']);
            $table->foreign(['institution_id', 'item_transfer_id'])->references(['institution_id', 'id'])->on('item_transfers')->restrictOnDelete();
            $table->foreign(['institution_id', 'item_unit_id'])->references(['institution_id', 'id'])->on('item_units')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transfer_items');
    }
};
