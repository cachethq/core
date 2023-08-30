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
        Schema::table('metrics', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->unsignedInteger('calc_type')->default(0)->change();
            $table->decimal('default_value', 10, 3)->nullable()->change();
        });
    }
};
