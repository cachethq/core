<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How many duplicate buckets are collapsed per pass.
     */
    private const CHUNK = 500;

    /**
     * Run the migrations.
     *
     * A point is now the bucket its observations accumulate into, so a metric
     * may only hold one row per timestamp. The read-then-insert ingest path
     * this replaces had no lock, so concurrent writes could leave duplicate
     * rows behind; they are collapsed before the constraint is added.
     */
    public function up(): void
    {
        $this->collapseDuplicateBuckets();

        if (Schema::hasIndex('metric_points', ['metric_id'])) {
            Schema::table('metric_points', function (Blueprint $table) {
                $table->dropIndex(['metric_id']);
            });
        }

        Schema::table('metric_points', function (Blueprint $table) {
            $table->unique(['metric_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metric_points', function (Blueprint $table) {
            $table->dropUnique(['metric_id', 'created_at']);
            $table->index('metric_id');
        });
    }

    /**
     * Merge rows sharing a metric and timestamp into the oldest of them.
     *
     * Each pass removes the buckets it collapses, so re-querying the first
     * chunk of duplicates always makes progress.
     */
    private function collapseDuplicateBuckets(): void
    {
        while (true) {
            $duplicates = DB::table('metric_points')
                ->select('metric_id', 'created_at')
                ->whereNotNull('created_at')
                ->groupBy('metric_id', 'created_at')
                ->havingRaw('count(*) > 1')
                ->limit(self::CHUNK)
                ->get();

            if ($duplicates->isEmpty()) {
                return;
            }

            foreach ($duplicates as $duplicate) {
                $this->collapseBucket($duplicate->metric_id, $duplicate->created_at);
            }
        }
    }

    /**
     * Collapse a single duplicated bucket.
     */
    private function collapseBucket(int|string $metricId, string $createdAt): void
    {
        /** @var Collection<int, object> $points */
        $points = DB::table('metric_points')
            ->where('metric_id', $metricId)
            ->where('created_at', $createdAt)
            ->orderBy('id')
            ->get();

        if ($points->count() < 2) {
            return;
        }

        DB::table('metric_points')->where('id', $points->first()->id)->update([
            'value' => $points->last()->value,
            'sum_value' => $points->sum(fn (object $point): float => (float) $point->sum_value),
            'counter' => $points->sum(fn (object $point): int => (int) $point->counter),
        ]);

        DB::table('metric_points')
            ->whereIn('id', $points->skip(1)->pluck('id')->all())
            ->delete();
    }
};
