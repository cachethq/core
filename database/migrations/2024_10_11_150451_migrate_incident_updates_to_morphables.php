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
        DB::table('incident_updates')
            ->update([
                'updateable_type' => 'incident',
                'updateable_id' => DB::raw('incident_id')
            ]);
    }
};
