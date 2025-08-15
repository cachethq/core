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
        Schema::table('components', function (Blueprint $table) {
            $table->boolean('checked')->default(false)->after('enabled');
            $table->timestamp('checked_at')->nullable()->after('checked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn([
                'checked',
                'checked_at',
            ]);
        });
    }
};
