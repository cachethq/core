<?php

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;

it('can bulk delete every supported resource', function (string $resource, string $ability, string $modelClass, string $table, bool $softDeletes) {
    Sanctum::actingAs(User::factory()->create(), [$ability]);

    $models = $modelClass::factory(2)->create();

    $response = deleteJson('/status/api/'.$resource.'?ids='.$models->pluck('id')->join(','));

    $response->assertNoContent();

    foreach ($models as $model) {
        if ($softDeletes) {
            $this->assertSoftDeleted($table, ['id' => $model->id]);
        } else {
            $this->assertDatabaseMissing($table, ['id' => $model->id]);
        }
    }
})->with([
    ['components', 'components.delete', Component::class, 'components', true],
    ['component-groups', 'component-groups.delete', ComponentGroup::class, 'component_groups', false],
    ['incidents', 'incidents.delete', Incident::class, 'incidents', true],
    ['incident-templates', 'incident-templates.delete', IncidentTemplate::class, 'incident_templates', false],
    ['metrics', 'metrics.delete', Metric::class, 'metrics', false],
    ['schedules', 'schedules.delete', Schedule::class, 'schedules', true],
]);

it('rejects collection deletion without valid IDs', function (string $query) {
    Sanctum::actingAs(User::factory()->create(), ['components.delete']);

    $response = deleteJson('/status/api/components'.$query);

    $response->assertUnprocessable()->assertInvalid('ids');
})->with([
    '',
    '?ids=',
    '?ids=all',
    '?ids=1,not-an-id',
    '?ids=1,',
]);

it('does not delete any resources when one requested ID does not exist', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.delete']);

    $component = Component::factory()->create();

    $response = deleteJson('/status/api/components?ids='.$component->id.',999999');

    $response->assertNotFound();
    $this->assertDatabaseHas('components', ['id' => $component->id, 'deleted_at' => null]);
});

it('requires authentication and the matching ability for collection deletion', function () {
    $component = Component::factory()->create();

    deleteJson('/status/api/components?ids='.$component->id)->assertUnauthorized();

    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    deleteJson('/status/api/components?ids='.$component->id)->assertForbidden();
    $this->assertDatabaseHas('components', ['id' => $component->id, 'deleted_at' => null]);
});

it('applies component group cleanup when bulk deleting component groups', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.delete']);

    $componentGroups = ComponentGroup::factory()->hasComponents(1)->count(2)->create();

    deleteJson('/status/api/component-groups?ids='.$componentGroups->pluck('id')->join(','))->assertNoContent();

    $this->assertDatabaseMissing('component_groups', ['id' => $componentGroups[0]->id]);
    $this->assertDatabaseMissing('component_groups', ['id' => $componentGroups[1]->id]);
    $this->assertDatabaseHas('components', ['component_group_id' => null]);
});

it('deletes incident updates when bulk deleting incidents', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.delete']);

    $incidents = Incident::factory()->hasUpdates(1)->count(2)->create();

    deleteJson('/status/api/incidents?ids='.$incidents->pluck('id')->join(','))->assertNoContent();

    $this->assertDatabaseMissing('updates', ['updateable_id' => $incidents[0]->id]);
    $this->assertDatabaseMissing('updates', ['updateable_id' => $incidents[1]->id]);
});

it('deletes metric points when bulk deleting metrics', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.delete']);

    $metrics = Metric::factory()->hasMetricPoints(1)->count(2)->create();

    deleteJson('/status/api/metrics?ids='.$metrics->pluck('id')->join(','))->assertNoContent();

    $this->assertDatabaseMissing('metric_points', ['metric_id' => $metrics[0]->id]);
    $this->assertDatabaseMissing('metric_points', ['metric_id' => $metrics[1]->id]);
});
