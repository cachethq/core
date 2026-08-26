<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The link is for display only: it groups a metric's chart under the
     * component it describes. Component status is driven by component
     * checks and incidents, never by metric points.
     */
    public function up(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->unsignedInteger('component_id')->nullable()->after('order');

            $table->index('component_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->dropIndex(['component_id']);
            $table->dropColumn('component_id');
        });
    }
};
