<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('department_id');
            $table->string('code', 50);
            $table->string('borrower_name', 150);
            $table->string('borrower_identifier', 100)->nullable();
            $table->string('borrower_department', 150)->nullable();
            $table->string('borrower_contact', 100)->nullable();
            $table->timestamp('borrowed_at');
            $table->timestamp('due_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->unique(['institution_id', 'code']);
            $table->unique(['institution_id', 'id']);
            $table->foreign(['institution_id', 'department_id'])->references(['institution_id', 'id'])->on('departments')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
