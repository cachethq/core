<?php

use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\Schema;

it('uses a component foreign key type compatible with the components table', function () {
    $migration = require dirname(__DIR__, 3).'/database/migrations/2025_09_14_000000_create_component_checks_table.php';
    $sql = [];

    Schema::shouldReceive('create')
        ->once()
        ->with('component_checks', \Mockery::on(function (callable $callback) use (&$sql): bool {
            $connection = new MySqlConnection(new \PDO('sqlite::memory:'), 'testing');
            $grammar = new MySqlGrammar($connection);
            $blueprint = new Blueprint('component_checks');

            $blueprint->create();
            $callback($blueprint);

            $sql = $blueprint->toSql($connection, $grammar);

            return true;
        }));

    $migration->up();

    $compiledSql = implode("\n", $sql);

    expect($compiledSql)
        ->toContain('`component_id` int unsigned not null')
        ->not->toContain('`component_id` bigint unsigned not null');
});
