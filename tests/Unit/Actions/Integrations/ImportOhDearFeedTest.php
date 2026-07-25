<?php

use Cachet\Actions\Integrations\ImportOhDearFeed;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ExternalProviderEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;

it('can import an Oh Dear feed', function () {
    $importOhDearFeed = app(ImportOhDearFeed::class);

    $data = json_decode(file_get_contents(__DIR__.'/../../../stubs/ohdear-feed-php.json'), true);

    $importOhDearFeed($data, importSites: true, componentGroupId: 1, importIncidents: true);

    $this->assertDatabaseHas('components', [
        'link' => 'https://www.php.net/',
        'name' => 'php.net',
        'component_group_id' => 1,
        'status' => ComponentStatusEnum::operational,
    ]);

    $this->assertDatabaseHas('incidents', [
        'external_provider' => ExternalProviderEnum::OhDear->value,
        'external_id' => '1274100',
        'name' => 'php.net has recovered.',
        'status' => IncidentStatusEnum::fixed,
    ]);
});

it('reguards models after importing incidents', function () {
    $importOhDearFeed = app(ImportOhDearFeed::class);

    $data = json_decode(file_get_contents(__DIR__.'/../../../stubs/ohdear-feed-php.json'), true);

    $importOhDearFeed($data, importSites: false, componentGroupId: null, importIncidents: true);

    expect(Incident::isUnguarded())->toBeFalse();
});
