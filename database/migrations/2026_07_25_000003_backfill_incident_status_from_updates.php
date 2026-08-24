<?php

use Cachet\Models\Incident;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An incident's status used to live in its updates and be resolved when
     * read; only a `fixed` update ever wrote the column. Now that the column is
     * canonical, any incident whose updates moved it on without resolving it
     * would revert to whatever status it was opened with. This aligns the
     * column with the newest status-bearing update, by the same rule the
     * application now keeps it in step with.
     */
    public function up(): void
    {
        $statuses = [];

        DB::table('updates')
            ->where('updateable_type', Relation::getMorphAlias(Incident::class))
            ->whereNotNull('status')
            ->orderBy('created_at')
            ->orderBy('id')
            ->select(['updateable_id', 'status'])
            ->chunk(1000, function ($updates) use (&$statuses): void {
                foreach ($updates as $update) {
                    $statuses[$update->updateable_id] = $update->status;
                }
            });

        collect($statuses)
            ->groupBy(fn (int $status): int => $status, preserveKeys: true)
            ->each(function ($incidents, int $status): void {
                $incidents->keys()->chunk(1000)->each(fn ($ids) => DB::table('incidents')
                    ->whereIn('id', $ids->all())
                    ->where(fn ($query) => $query->where('status', '!=', $status)->orWhereNull('status'))
                    ->update(['status' => $status]));
            });
    }

    /**
     * Reverse the migrations.
     *
     * The previous statuses were never stored, so there is nothing to restore.
     */
    public function down(): void
    {
        //
    }
};
