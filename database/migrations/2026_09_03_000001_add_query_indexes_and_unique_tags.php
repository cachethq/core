<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->deduplicateTags();
        $this->deduplicateTaggables();
        $this->replaceTagUniqueIndex();

        Schema::table('taggables', function (Blueprint $table): void {
            $table->unique(
                ['tag_id', 'taggable_type', 'taggable_id'],
                'taggables_tag_unique',
            );
        });

        Schema::table('components', function (Blueprint $table): void {
            $table->index(['enabled', 'checked', 'id'], 'components_monitoring_index');
        });

        Schema::table('component_checks', function (Blueprint $table): void {
            $table->index(['component_id', 'checked_at'], 'component_checks_component_checked_index');
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'incidents_status_created_index');
            $table->index(['stickied', 'occurred_at'], 'incidents_timeline_occurred_index');
            $table->index(['stickied', 'created_at'], 'incidents_timeline_created_index');
            $table->index(['notifications', 'published_notified_at', 'published_at'], 'incidents_publish_index');
        });

        Schema::table('updates', function (Blueprint $table): void {
            $table->index(['updateable_type', 'updateable_id', 'created_at', 'id'], 'updates_updateable_created_index');
        });

        Schema::table('schedules', function (Blueprint $table): void {
            $table->index('completed_at', 'schedules_completed_at_index');
            $table->index('scheduled_at', 'schedules_scheduled_at_index');
            $table->index(['notifications', 'published_notified_at', 'published_at'], 'schedules_publish_index');
        });

        Schema::table('schedule_components', function (Blueprint $table): void {
            $table->index('component_id', 'schedule_components_component_id_index');
        });

        Schema::table('webhook_attempts', function (Blueprint $table): void {
            $table->index(['subscription_id', 'created_at'], 'webhook_attempts_subscription_created_index');
            $table->index('created_at', 'webhook_attempts_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('taggables', function (Blueprint $table): void {
            $table->dropUnique('taggables_tag_unique');
        });

        Schema::table('components', function (Blueprint $table): void {
            $table->dropIndex('components_monitoring_index');
        });

        Schema::table('component_checks', function (Blueprint $table): void {
            $table->dropIndex('component_checks_component_checked_index');
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropIndex('incidents_status_created_index');
            $table->dropIndex('incidents_timeline_occurred_index');
            $table->dropIndex('incidents_timeline_created_index');
            $table->dropIndex('incidents_publish_index');
        });

        Schema::table('updates', function (Blueprint $table): void {
            $table->dropIndex('updates_updateable_created_index');
        });

        Schema::table('schedules', function (Blueprint $table): void {
            $table->dropIndex('schedules_completed_at_index');
            $table->dropIndex('schedules_scheduled_at_index');
            $table->dropIndex('schedules_publish_index');
        });

        Schema::table('schedule_components', function (Blueprint $table): void {
            $table->dropIndex('schedule_components_component_id_index');
        });

        Schema::table('webhook_attempts', function (Blueprint $table): void {
            $table->dropIndex('webhook_attempts_subscription_created_index');
            $table->dropIndex('webhook_attempts_created_at_index');
        });

        if ($this->hasIndex('tags', 'tags_slug_unique_v4')) {
            Schema::table('tags', function (Blueprint $table): void {
                $table->dropUnique('tags_slug_unique_v4');
            });
        }

        if (! $this->hasUniqueIndex('tags', ['name', 'slug'])) {
            Schema::table('tags', function (Blueprint $table): void {
                $table->unique(['name', 'slug']);
            });
        }
    }

    private function deduplicateTags(): void
    {
        DB::table('tags')
            ->select('slug')
            ->groupBy('slug')
            ->havingRaw('count(*) > 1')
            ->orderBy('slug')
            ->get()
            ->each(function (object $duplicate): void {
                $tagIds = DB::table('tags')
                    ->where('slug', $duplicate->slug)
                    ->orderBy('id')
                    ->pluck('id');
                $canonicalTagId = $tagIds->shift();

                $tagIds->each(function (int $tagId) use ($canonicalTagId): void {
                    if (Schema::hasTable('taggables')) {
                        DB::table('taggables')
                            ->where('tag_id', $tagId)
                            ->get()
                            ->each(function (object $taggable) use ($canonicalTagId): void {
                                DB::table('taggables')->insertOrIgnore([
                                    'tag_id' => $canonicalTagId,
                                    'taggable_type' => $taggable->taggable_type,
                                    'taggable_id' => $taggable->taggable_id,
                                    'created_at' => $taggable->created_at,
                                    'updated_at' => $taggable->updated_at,
                                ]);
                            });

                        DB::table('taggables')->where('tag_id', $tagId)->delete();
                    }

                    DB::table('tags')->where('id', $tagId)->delete();
                });
            });
    }

    private function replaceTagUniqueIndex(): void
    {
        if (! $this->hasUniqueIndex('tags', ['slug'])) {
            Schema::table('tags', function (Blueprint $table): void {
                $table->unique('slug', 'tags_slug_unique_v4');
            });
        }

        if ($this->hasIndex('tags', 'tags_name_slug_unique')) {
            Schema::table('tags', function (Blueprint $table): void {
                $table->dropUnique('tags_name_slug_unique');
            });
        }
    }

    private function deduplicateTaggables(): void
    {
        if (! Schema::hasTable('taggables')) {
            return;
        }

        DB::table('taggables')
            ->select(['tag_id', 'taggable_type', 'taggable_id'])
            ->groupBy(['tag_id', 'taggable_type', 'taggable_id'])
            ->havingRaw('count(*) > 1')
            ->get()
            ->each(function (object $duplicate): void {
                $duplicateIds = DB::table('taggables')
                    ->where('tag_id', $duplicate->tag_id)
                    ->where('taggable_type', $duplicate->taggable_type)
                    ->where('taggable_id', $duplicate->taggable_id)
                    ->orderBy('id')
                    ->pluck('id');

                $duplicateIds->shift();

                DB::table('taggables')->whereIn('id', $duplicateIds)->delete();
            });
    }

    /**
     * @param  list<string>  $columns
     */
    private function hasUniqueIndex(string $table, array $columns): bool
    {
        return collect(Schema::getIndexes($table))->contains(
            fn (array $index): bool => $index['unique'] && $index['columns'] === $columns,
        );
    }

    private function hasIndex(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))->contains(
            fn (array $index): bool => $index['name'] === $name,
        );
    }
};
