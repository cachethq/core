<?php

use Cachet\Enums\ResourceOrderColumnEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('component_groups', function (Blueprint $table) {
            $table->string('order_column')->nullable()->after('order');
            $table->char('order_direction', 4)->nullable()->after('order_column');
        });

        DB::table('component_groups')->update(['order_column' => ResourceOrderColumnEnum::Manual->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('component_groups', function (Blueprint $table) {
            $table->dropColumn([
                'order_column',
                'order_direction',
            ]);
        });
    }
};
