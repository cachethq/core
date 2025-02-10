<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incident_components', function (Blueprint $table) {
            $table->renameColumn('status_id', 'status');
            $table->unsignedInteger('status')->nullable()->change();
        });
    }
};
