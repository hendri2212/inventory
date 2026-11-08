<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_unit_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_unit_id')->constrained()->cascadeOnDelete();
            $table->string('path');
        });

        DB::table('item_unit_photos')->insertUsing(['item_unit_id', 'path'],
            DB::table('item_units')->whereNotNull('photo_path')->select('id', 'photo_path'));

        Schema::table('item_units', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }

    public function down(): void
    {
        if (DB::table('item_unit_photos')->select('item_unit_id')->groupBy('item_unit_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Rollback dibatalkan: ada unit dengan lebih dari satu foto.');
        }

        Schema::table('item_units', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
        });

        DB::table('item_units')->update(['photo_path' => DB::table('item_unit_photos')
            ->select('path')->whereColumn('item_unit_id', 'item_units.id')->limit(1)]);

        Schema::dropIfExists('item_unit_photos');
    }
};
