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
        DB::table('schedules')->insertUsing([
            'name', 'message', 'scheduled_at', 'created_at', 'updated_at',
        ], DB::table('incidents')
            ->select('name', 'message', 'scheduled_at', 'created_at', 'updated_at')
            ->whereNotNull('scheduled_at')
        );

        DB::table('incidents')->whereNotNull('scheduled_at')->delete();

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->before('created_at')->nullable()->default(null);
        });
    }
};
