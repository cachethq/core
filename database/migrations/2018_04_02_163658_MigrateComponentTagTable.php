<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $tags = DB::table('component_tag')->get()->map(function ($tag) {
            return [
                'tag_id' => $tag->tag_id,
                'taggable_type' => 'components',
                'taggable_id' => $tag->component_id,
            ];
        });

        DB::table('taggables')->insert($tags->toArray());

        Schema::dropIfExists('component_tag');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('component_tag', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('component_id');
            $table->integer('tag_id');

            $table->index('component_id');
            $table->index('tag_id');
        });
    }
};
