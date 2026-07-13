<?php

namespace Cachet\Mcp;

use Cachet\Cachet;
use Cachet\Mcp\Tools\ComponentGroups\CreateComponentGroup;
use Cachet\Mcp\Tools\ComponentGroups\DeleteComponentGroup;
use Cachet\Mcp\Tools\ComponentGroups\ListComponentGroups;
use Cachet\Mcp\Tools\ComponentGroups\UpdateComponentGroup;
use Cachet\Mcp\Tools\Components\CreateComponent;
use Cachet\Mcp\Tools\Components\DeleteComponent;
use Cachet\Mcp\Tools\Components\GetComponent;
use Cachet\Mcp\Tools\Components\ListComponents;
use Cachet\Mcp\Tools\Components\UpdateComponent;
use Cachet\Mcp\Tools\Incidents\CreateIncident;
use Cachet\Mcp\Tools\Incidents\DeleteIncident;
use Cachet\Mcp\Tools\Incidents\GetIncident;
use Cachet\Mcp\Tools\Incidents\ListIncidents;
use Cachet\Mcp\Tools\Incidents\UpdateIncident;
use Cachet\Mcp\Tools\IncidentTemplates\CreateIncidentTemplate;
use Cachet\Mcp\Tools\IncidentTemplates\DeleteIncidentTemplate;
use Cachet\Mcp\Tools\IncidentTemplates\ListIncidentTemplates;
use Cachet\Mcp\Tools\IncidentTemplates\UpdateIncidentTemplate;
use Cachet\Mcp\Tools\IncidentUpdates\DeleteIncidentUpdate;
use Cachet\Mcp\Tools\IncidentUpdates\EditIncidentUpdate;
use Cachet\Mcp\Tools\IncidentUpdates\RecordIncidentUpdate;
use Cachet\Mcp\Tools\MetricPoints\AddMetricPoint;
use Cachet\Mcp\Tools\MetricPoints\DeleteMetricPoint;
use Cachet\Mcp\Tools\Metrics\CreateMetric;
use Cachet\Mcp\Tools\Metrics\DeleteMetric;
use Cachet\Mcp\Tools\Metrics\GetMetric;
use Cachet\Mcp\Tools\Metrics\ListMetrics;
use Cachet\Mcp\Tools\Metrics\UpdateMetric;
use Cachet\Mcp\Tools\Schedules\CreateSchedule;
use Cachet\Mcp\Tools\Schedules\DeleteSchedule;
use Cachet\Mcp\Tools\Schedules\GetSchedule;
use Cachet\Mcp\Tools\Schedules\ListSchedules;
use Cachet\Mcp\Tools\Schedules\UpdateSchedule;
use Cachet\Mcp\Tools\ScheduleUpdates\DeleteScheduleUpdate;
use Cachet\Mcp\Tools\ScheduleUpdates\EditScheduleUpdate;
use Cachet\Mcp\Tools\ScheduleUpdates\RecordScheduleUpdate;
use Cachet\Mcp\Tools\Status\GetStatus;
use Cachet\Mcp\Tools\Subscribers\CreateSubscriber;
use Cachet\Mcp\Tools\Subscribers\ListSubscribers;
use Cachet\Mcp\Tools\Subscribers\UnsubscribeSubscriber;
use Cachet\Mcp\Tools\Subscribers\UpdateSubscriber;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Contracts\Transport;
use Laravel\Mcp\Server\Tool;

class CachetServer extends Server
{
    protected string $name = 'Cachet';

    protected string $instructions = <<<'MARKDOWN'
        This server exposes the Cachet status page dashboard over the Model Context Protocol:
        manage components, component groups, incidents and their updates, incident templates,
        maintenance schedules and their updates, metrics and metric points, and subscribers.

        Read tools are available to every session. Write tools appear only when the session is
        authenticated with a Cachet API token holding the matching ability (for example
        `incidents.manage` or `components.delete`). API tokens are created from the Cachet
        dashboard under Settings, API Keys, and are sent as a bearer token.

        Statuses are integers. Component status: 1 operational, 2 performance issues,
        3 partial outage, 4 major outage, 5 unknown, 6 under maintenance. Incident status:
        0 unknown, 1 investigating, 2 identified, 3 watching, 4 fixed. Recording an incident
        update with status 4 (fixed) resolves the incident and returns its affected components
        to operational.
        MARKDOWN;

    /**
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        GetStatus::class,
        ListComponents::class,
        GetComponent::class,
        CreateComponent::class,
        UpdateComponent::class,
        DeleteComponent::class,
        ListComponentGroups::class,
        CreateComponentGroup::class,
        UpdateComponentGroup::class,
        DeleteComponentGroup::class,
        ListIncidents::class,
        GetIncident::class,
        CreateIncident::class,
        UpdateIncident::class,
        DeleteIncident::class,
        RecordIncidentUpdate::class,
        EditIncidentUpdate::class,
        DeleteIncidentUpdate::class,
        ListIncidentTemplates::class,
        CreateIncidentTemplate::class,
        UpdateIncidentTemplate::class,
        DeleteIncidentTemplate::class,
        ListSchedules::class,
        GetSchedule::class,
        CreateSchedule::class,
        UpdateSchedule::class,
        DeleteSchedule::class,
        RecordScheduleUpdate::class,
        EditScheduleUpdate::class,
        DeleteScheduleUpdate::class,
        ListMetrics::class,
        GetMetric::class,
        CreateMetric::class,
        UpdateMetric::class,
        DeleteMetric::class,
        AddMetricPoint::class,
        DeleteMetricPoint::class,
        ListSubscribers::class,
        CreateSubscriber::class,
        UpdateSubscriber::class,
        UnsubscribeSubscriber::class,
    ];

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);

        $this->version = Cachet::version();
    }
}
