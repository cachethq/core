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
        Schema::table('incident_templates', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $table->string('engine')->default('twig')->after('template');
            } else {
                $table->char('engine')->default('twig')->after('template');
            }
        });
    }
};
