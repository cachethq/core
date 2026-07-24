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
        Schema::table('incidents', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->timestamp('published_notified_at')->nullable();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->timestamp('published_notified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'published_notified_at']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'published_notified_at']);
        });
    }
};
