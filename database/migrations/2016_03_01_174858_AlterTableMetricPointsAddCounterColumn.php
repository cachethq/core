<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->integer('threshold')->unsigned()->default(5)->after('default_view');
        });

        Schema::table('metric_points', function (Blueprint $table) {
            $table->integer('counter')->unsigned()->default(1)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->dropColumn('threshold');
        });

        Schema::table('metric_points', function (Blueprint $table) {
            $table->dropColumn('counter');
        });
    }
};
