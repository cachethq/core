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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->after('id')->default('cachet');
            $table->boolean('locked')->default(false)->after('name');
            $table->json('payload')->after('locked');

            $table->text('value')->nullable()->change();

            $table->unique(['group', 'name']);
        });
    }
};
