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
        Schema::table('incidents', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->timestamp('published_notified_at')->nullable();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->timestamp('published_notified_at')->nullable();
        });

        // Existing incidents and schedules were published the moment they were
        // created, so treat their publish timestamp as their creation time.
        // published_notified_at is backfilled in lockstep so the publish
        // notification command never re-announces historical records.
        foreach (['incidents', 'schedules'] as $table) {
            DB::table($table)->whereNull('published_at')->update([
                'published_at' => DB::raw('created_at'),
                'published_notified_at' => DB::raw('created_at'),
            ]);
        }
    }
};
