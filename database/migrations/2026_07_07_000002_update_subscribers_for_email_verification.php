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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->renameColumn('verified_at', 'email_verified_at');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('verify_code');
        });

        DB::table('subscribers')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->renameColumn('email_verified_at', 'verified_at');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('verify_code')->default('');
        });
    }
};
