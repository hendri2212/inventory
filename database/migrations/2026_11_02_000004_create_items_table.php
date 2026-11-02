<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('department_id');
            $table->foreignId('category_id');
            $table->string('code', 50);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit', 30)->default('unit');
            $table->unsignedSmallInteger('purchase_year')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('condition', ['good', 'minor_damage', 'major_damage'])->nullable();
            $table->text('condition_notes')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->text('disposal_notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unique(['institution_id', 'code']);
            $table->index(['institution_id', 'department_id']);
            $table->index(['institution_id', 'category_id']);
            $table->foreign(['institution_id', 'department_id'])
                ->references(['institution_id', 'id'])->on('departments')
                ->restrictOnDelete();
            $table->foreign(['institution_id', 'category_id'])
                ->references(['institution_id', 'id'])->on('categories')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
