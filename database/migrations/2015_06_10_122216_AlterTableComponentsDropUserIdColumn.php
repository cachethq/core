<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            // This seems to break in Laravel 11, so we'll conditionally delete the column.
            if (Schema::hasColumn('components', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->default(null)->after('group_id');
        });
    }
};
