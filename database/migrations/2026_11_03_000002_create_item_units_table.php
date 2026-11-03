<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('item_id');
            $table->foreignId('department_id');
            $table->foreignId('room_id')->nullable();
            $table->string('code', 50);
            $table->string('serial_number', 150)->nullable();
            $table->unsignedSmallInteger('purchase_year')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->enum('funding_source', ['BOSP', 'BOSDA', 'Bantuan'])->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('condition', ['good', 'minor_damage', 'major_damage'])->nullable();
            $table->text('condition_notes')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->text('disposal_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['institution_id', 'code']);
            $table->unique(['institution_id', 'id']);
            $table->index(['institution_id', 'department_id']);
            $table->foreign(['institution_id', 'item_id'])->references(['institution_id', 'id'])->on('items')->restrictOnDelete();
            $table->foreign(['institution_id', 'department_id'])->references(['institution_id', 'id'])->on('departments')->restrictOnDelete();
            $table->foreign(['institution_id', 'room_id'])->references(['institution_id', 'id'])->on('rooms')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
