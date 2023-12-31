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
        Schema::create('incidents', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('component_id')->default(0);
            $table->string('name');
            $table->integer('status');
            $table->longText('message');
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('component_id');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('incidents');
    }
};
