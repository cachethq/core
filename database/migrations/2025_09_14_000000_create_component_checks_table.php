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
        Schema::create('component_checks', function (Blueprint $table) {
            $table->id();

            if ($this->componentsTableUsesBigIntegerIds()) {
                $table->unsignedBigInteger('component_id');
            } else {
                $table->unsignedInteger('component_id');
            }

            $table->unsignedTinyInteger('status');
            $table->boolean('successful')->default(false);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedInteger('response_time')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->foreign('component_id')->references('id')->on('components')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_checks');
    }

    /**
     * Whether the components table keys its rows by big integers.
     *
     * components.id is an unsigned integer on fresh installations and on
     * upgrades from 2.x, but a big integer on databases created by early 3.x
     * builds. MySQL refuses a foreign key whose column type does not match
     * the column it references, so component_id must mirror the parent.
     */
    private function componentsTableUsesBigIntegerIds(): bool
    {
        return in_array(Schema::getColumnType('components', 'id'), ['bigint', 'int8'], true);
    }
};
