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
        Schema::create('component_status_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('component_id');
            $table->unsignedTinyInteger('old_status')->nullable();
            $table->unsignedTinyInteger('new_status');
            $table->string('source');
            $table->nullableMorphs('causer');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('component_id')->references('id')->on('components')->cascadeOnDelete();
            $table->index(['component_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_status_changes');
    }
};
