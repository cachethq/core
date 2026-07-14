<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The Blade template engine has been removed as it compiles to arbitrary
     * PHP and cannot be safely rendered from untrusted input. Existing Blade
     * templates are migrated onto the sandboxed Twig engine.
     */
    public function up(): void
    {
        DB::table('incident_templates')
            ->where('engine', 'blade')
            ->update(['engine' => 'twig']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
