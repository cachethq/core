<?php

namespace Cachet\Enums;

enum WebhookEventEnum: string
{
    case component_created = 'component_created';
    case component_updated = 'component_updated';
    case component_deleted = 'component_deleted';
    case component_status_changed = 'component_status_changed';

    case incident_created = 'incident_created';
    case incident_updated = 'incident_updated';
    case incident_deleted = 'incident_deleted';

    case metric_created = 'metric_created';
    case metric_updated = 'metric_updated';
    case metric_deleted = 'metric_deleted';
    case metric_point_created = 'metric_point_created';
    case metric_point_deleted = 'metric_point_deleted';

    case subscriber_created = 'subscriber_created';
    case subscriber_unsubscribed = 'subscriber_unsubscribed';
    case subscriber_verified = 'subscriber_verified';
}
