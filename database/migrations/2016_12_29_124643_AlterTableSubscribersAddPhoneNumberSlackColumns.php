<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('email')->nullable()->default(null)->change();
            $table->string('phone_number')->nullable()->default(null)->after('verify_code');
            $table->string('slack_webhook_url')->nullable()->default(null)->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'slack_webhook_url']);
        });
    }
};
