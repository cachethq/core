<?php

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The pivots that link a component to whatever is impacting it.
     *
     * @var array<string, string>
     */
    private const PIVOTS = [
        'incident_components' => 'incident_id',
        'schedule_components' => 'schedule_id',
    ];

    /**
     * Run the migrations.
     *
     * Now that these rows decide what the status page displays, a component may
     * only be attached to a given incident or schedule once. Duplicates that
     * predate the constraint are collapsed to their most severe impact, which
     * is the one that would have won anyway.
     */
    public function up(): void
    {
        foreach (self::PIVOTS as $table => $parentKey) {
            $this->deduplicate($table, $parentKey);

            Schema::table($table, function (Blueprint $table) use ($parentKey) {
                $table->unique([$parentKey, 'component_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::PIVOTS as $table => $parentKey) {
            Schema::table($table, function (Blueprint $table) use ($parentKey) {
                $table->dropUnique([$parentKey, 'component_id']);
            });
        }
    }

    /**
     * Collapse duplicate rows, keeping the most severe impact of each pair.
     */
    private function deduplicate(string $table, string $parentKey): void
    {
        DB::table($table)
            ->select($parentKey, 'component_id')
            ->groupBy($parentKey, 'component_id')
            ->havingRaw('count(*) > 1')
            ->get()
            ->each(function (object $duplicate) use ($table, $parentKey) {
                $keep = DB::table($table)
                    ->where($parentKey, $duplicate->{$parentKey})
                    ->where('component_id', $duplicate->component_id)
                    ->get()
                    ->sortByDesc(fn (object $row) => [
                        ComponentStatusEnum::tryFrom((int) $row->component_status)?->severity() ?? 0,
                        $row->id,
                    ])
                    ->first();

                DB::table($table)
                    ->where($parentKey, $duplicate->{$parentKey})
                    ->where('component_id', $duplicate->component_id)
                    ->where('id', '!=', $keep->id)
                    ->delete();
            });
    }
};
