<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('loan_id');
            $table->foreignId('item_unit_id');
            // NULL after return; unique value while on loan prevents double lending.
            $table->foreignId('active_item_unit_id')->nullable()->unique();
            $table->enum('condition_out', ['good', 'minor_damage', 'major_damage']);
            $table->text('notes_out')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->enum('condition_in', ['good', 'minor_damage', 'major_damage'])->nullable();
            $table->text('notes_in')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->unique(['loan_id', 'item_unit_id']);
            $table->foreign(['institution_id', 'loan_id'])->references(['institution_id', 'id'])->on('loans')->restrictOnDelete();
            $table->foreign(['institution_id', 'item_unit_id'])->references(['institution_id', 'id'])->on('item_units')->restrictOnDelete();
            $table->foreign(['institution_id', 'active_item_unit_id'])->references(['institution_id', 'id'])->on('item_units')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
