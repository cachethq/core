<?php

use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Incidents now keep a separate baseline status so the current status can
     * follow timeline updates without losing what the incident should revert to
     * when those updates are edited away or deleted.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedInteger('baseline_status')->nullable()->after('status');
        });

        DB::table('incidents')
            ->whereNotNull('status')
            ->update(['baseline_status' => DB::raw('status')]);

        DB::table('incidents')
            ->whereNull('status')
            ->update(['baseline_status' => IncidentStatusEnum::unknown->value]);

        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedInteger('baseline_status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('baseline_status');
        });
    }
};
