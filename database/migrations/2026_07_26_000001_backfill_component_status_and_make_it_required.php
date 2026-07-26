<?php

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Component status used to be nullable and was treated as "unknown" at read
     * time. Backfill those rows explicitly so the column can become required
     * and callers no longer need to carry null-specific logic forever.
     */
    public function up(): void
    {
        DB::table('components')
            ->whereNull('status')
            ->update(['status' => ComponentStatusEnum::unknown->value]);

        Schema::table('components', function (Blueprint $table) {
            $table->unsignedInteger('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * The original nulls were not preserved, so only the schema nullability can
     * be restored here.
     */
    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable()->change();
        });
    }
};
