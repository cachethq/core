<?php

use Illuminate\Support\Facades\Schema;

it('creates component_id with the same column type as components.id', function () {
    $componentsId = collect(Schema::getColumns('components'))->firstWhere('name', 'id');
    $componentId = collect(Schema::getColumns('component_checks'))->firstWhere('name', 'component_id');

    expect($componentId['type'])->toBe($componentsId['type']);
});

it('constrains component_id to components.id with cascading deletes', function () {
    $foreignKey = collect(Schema::getForeignKeys('component_checks'))
        ->first(fn (array $key) => $key['columns'] === ['component_id']);

    expect($foreignKey)->not->toBeNull()
        ->and($foreignKey['foreign_table'])->toBe('components')
        ->and($foreignKey['foreign_columns'])->toBe(['id'])
        ->and(strtolower($foreignKey['on_delete']))->toBe('cascade');
});
