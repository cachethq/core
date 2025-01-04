<?php

use Cachet\Models\WebhookSubscription;
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
        Schema::create('webhook_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WebhookSubscription::class, 'subscription_id')->onDelete('cascade');
            $table->string('event');
            $table->unsignedTinyInteger('attempt');
            $table->json('payload');
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedTinyInteger('transfer_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('webhook_attempts');
    }
};
