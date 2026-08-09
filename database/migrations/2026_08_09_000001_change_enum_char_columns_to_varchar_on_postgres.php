<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        Schema::table('component_groups', function (Blueprint $table) {
            $table->string('order_direction', 4)->nullable()->change();
        });

        Schema::table('incident_templates', function (Blueprint $table) {
            $table->string('engine')->default('twig')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        Schema::table('component_groups', function (Blueprint $table) {
            $table->char('order_direction', 4)->nullable()->change();
        });

        Schema::table('incident_templates', function (Blueprint $table) {
            $table->char('engine')->default('twig')->change();
        });
    }
};
