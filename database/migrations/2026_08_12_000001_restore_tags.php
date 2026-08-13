<?php

use Cachet\Models\Component;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('taggables')) {
            Schema::create('taggables', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('tag_id')->index();
                $table->morphs('taggable');
                $table->timestamps();
            });
        }

        DB::table('taggables')
            ->select('tag_id', 'taggable_id', 'taggable_type')
            ->groupBy('tag_id', 'taggable_id', 'taggable_type')
            ->havingRaw('count(*) > 1')
            ->orderBy('tag_id')
            ->orderBy('taggable_id')
            ->orderBy('taggable_type')
            ->each(function (object $duplicate): void {
                $ids = DB::table('taggables')
                    ->where('tag_id', $duplicate->tag_id)
                    ->where('taggable_id', $duplicate->taggable_id)
                    ->where('taggable_type', $duplicate->taggable_type)
                    ->orderBy('id')
                    ->pluck('id')
                    ->all();

                array_shift($ids);

                DB::table('taggables')->whereIn('id', $ids)->delete();
            });

        Schema::table('taggables', function (Blueprint $table) {
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });

        DB::table('taggables')
            ->where('taggable_type', 'components')
            ->update(['taggable_type' => Component::class]);
    }

    public function down(): void
    {
        DB::table('taggables')
            ->where('taggable_type', Component::class)
            ->update(['taggable_type' => 'components']);
    }
};
