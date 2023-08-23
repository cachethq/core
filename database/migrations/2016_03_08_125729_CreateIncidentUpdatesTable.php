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
        Schema::create('incident_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incident_id')->unsigned();
            $table->integer('status');
            $table->longText('message');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->index('incident_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('incident_updates');
    }
};
