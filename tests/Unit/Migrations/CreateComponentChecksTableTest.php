<?php

it('uses a component foreign key type compatible with the components table', function () {
    $migration = file_get_contents(dirname(__DIR__, 3).'/database/migrations/2025_09_14_000000_create_component_checks_table.php');

    expect($migration)
        ->toContain("\$table->unsignedInteger('component_id');")
        ->toContain("\$table->foreign('component_id')->references('id')->on('components')->cascadeOnDelete();")
        ->not->toContain("\$table->foreignId('component_id')");
});
