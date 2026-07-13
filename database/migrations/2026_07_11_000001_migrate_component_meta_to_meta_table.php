<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The morph aliases written by the metadata feature this migration enables.
     *
     * Hardcoded rather than read from the runtime morph map so this migration
     * stays stable as application code evolves.
     *
     * @var list<string>
     */
    private array $morphAliases = [
        'component',
        'component_group',
        'incident',
        'schedule',
        'subscriber',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meta', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });

        DB::table('components')
            ->whereNotNull('meta')
            ->orderBy('id')
            ->each(function (object $component) {
                $meta = json_decode($component->meta, true);

                if (! is_array($meta)) {
                    Log::warning('Skipping component meta that is not a key/value object during migration.', [
                        'component_id' => $component->id,
                        'meta' => $component->meta,
                    ]);

                    return;
                }

                $rows = collect($meta)->map(fn (mixed $value, int|string $key) => [
                    'key' => (string) $key,
                    'value' => json_encode($value),
                    'meta_id' => $component->id,
                    'meta_type' => 'component',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->values()->all();

                DB::table('meta')->insert($rows);
            });

        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->longText('meta')->nullable()->default(null)->after('enabled');
        });

        DB::table('meta')
            ->where('meta_type', 'component')
            ->orderBy('meta_id')
            ->get()
            ->groupBy('meta_id')
            ->each(function ($rows, $componentId) {
                $meta = $rows->mapWithKeys(fn (object $row) => [
                    $row->key => json_decode($row->value, true),
                ])->all();

                DB::table('components')
                    ->where('id', $componentId)
                    ->update(['meta' => json_encode($meta)]);
            });

        DB::table('meta')->whereIn('meta_type', $this->morphAliases)->delete();

        Schema::table('meta', function (Blueprint $table) {
            $table->string('value')->nullable(false)->change();
        });
    }
};
