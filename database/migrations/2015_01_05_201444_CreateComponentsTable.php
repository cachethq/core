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
        Schema::create('components', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->text('link');
            $table->integer('status');
            $table->integer('order');
            $table->integer('group_id');
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('group_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('components');
    }
};
