<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable()->after('id');
        });

        DB::table('incidents')->eachById(function ($incident) {
            DB::table('incidents')->where('id', $incident->id)->update(['guid' => Str::uuid()]);
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->uuid('guid')->nullable(false)->change();
        });
    }
};
