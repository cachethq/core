<?php

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
        Schema::table('metric_points', function (Blueprint $table) {
            $table->decimal('sum_value', 20, 3)->default(0)->after('value');
        });

        $connection = Schema::getConnection();
        $grammar = $connection->getQueryGrammar();

        $connection->statement(sprintf(
            'update %s set %s = %s * %s',
            $grammar->wrapTable('metric_points'),
            $grammar->wrap('sum_value'),
            $grammar->wrap('value'),
            $grammar->wrap('counter'),
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metric_points', function (Blueprint $table) {
            $table->dropColumn('sum_value');
        });
    }
};
