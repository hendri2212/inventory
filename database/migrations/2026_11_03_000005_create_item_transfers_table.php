<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->string('code', 50);
            $table->enum('type', ['room', 'department']);
            $table->foreignId('from_department_id');
            $table->foreignId('to_department_id');
            $table->foreignId('from_room_id')->nullable();
            $table->foreignId('to_room_id')->nullable();
            $table->timestamp('transferred_at');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->unique(['institution_id', 'code']);
            $table->unique(['institution_id', 'id']);
            $table->foreign(['institution_id', 'from_department_id'])->references(['institution_id', 'id'])->on('departments')->restrictOnDelete();
            $table->foreign(['institution_id', 'to_department_id'])->references(['institution_id', 'id'])->on('departments')->restrictOnDelete();
            $table->foreign(['institution_id', 'from_room_id'])->references(['institution_id', 'id'])->on('rooms')->restrictOnDelete();
            $table->foreign(['institution_id', 'to_room_id'])->references(['institution_id', 'id'])->on('rooms')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transfers');
    }
};
