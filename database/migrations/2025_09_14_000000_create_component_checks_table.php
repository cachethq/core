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
        Schema::create('component_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('component_id');
            $table->unsignedTinyInteger('status');
            $table->boolean('successful')->default(false);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedInteger('response_time')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->foreign('component_id')->references('id')->on('components')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_checks');
    }
};
