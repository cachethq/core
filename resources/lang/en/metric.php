<?php

return [
    'resource_label' => 'Metric|Metrics',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'suffix' => 'Suffix',
            'default_value' => 'Default value',
            'calc_type' => 'Metric type',
            'display_chart' => 'Display chart',
            'places' => 'Places',
            'default_view' => 'Default view',
            'threshold' => 'Threshold',
            'order' => 'Order',
            'component' => 'Component',
            'visible' => 'Visible',
            'points_count' => 'Points count',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ],
        'empty_state' => [
            'heading' => 'Metrics',
            'description' => 'Metrics are used to track and display data.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'suffix_label' => 'Suffix',
        'description_label' => 'Description',
        'default_view_label' => 'Default view',
        'default_value_label' => 'Default value',
        'calc_type_label' => 'Metric type',
        'places_label' => 'Places',
        'threshold_label' => 'Threshold',
        'component_label' => 'Component',
        'component_helper_text' => 'Display this metric alongside a component. Metrics never affect a component\'s status.',

        'visible_label' => 'Visible',
        'display_chart_label' => 'Display chart',
        'show_when_empty_label' => 'Show when empty',
    ],
    'overview' => [
        'metric_points_label' => 'Metric points',
        'metric_points_description' => 'Recent metric points.',
    ],
    'sum_label' => 'Sum',
    'average_label' => 'Average',
    'view_labels' => [
        'last_hour' => 'Last hour',
        'today' => 'Today',
        'week' => 'Week',
        'month' => 'Month',
    ],
];
