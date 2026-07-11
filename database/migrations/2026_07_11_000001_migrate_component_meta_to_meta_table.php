<?php

use Cachet\Models\Component;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meta', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });

        $type = Relation::getMorphAlias(Component::class);

        DB::table('components')
            ->whereNotNull('meta')
            ->orderBy('id')
            ->each(function (object $component) use ($type) {
                $meta = json_decode($component->meta, true);

                if (! is_array($meta)) {
                    return;
                }

                foreach ($meta as $key => $value) {
                    DB::table('meta')->insert([
                        'key' => (string) $key,
                        'value' => json_encode($value),
                        'meta_id' => $component->id,
                        'meta_type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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

        $type = Relation::getMorphAlias(Component::class);

        DB::table('meta')
            ->where('meta_type', $type)
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

        DB::table('meta')->where('meta_type', $type)->delete();

        Schema::table('meta', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }
};
