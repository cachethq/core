<?php

return [
    'resource_label' => 'Metric|Metrics',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'suffix' => 'Suffix',
            'default_value' => 'Default Value',
            'calc_type' => 'Metric Type',
            'display_chart' => 'Display Chart',
            'places' => 'Places',
            'default_view' => 'Default View',
            'threshold' => 'Threshold',
            'order' => 'Order',
            'visible' => 'Visible',
            'points_count' => 'Points Count',
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
        'default_view_label' => 'Default View',
        'default_value_label' => 'Default Value',
        'calc_type_label' => 'Metric Type',
        'places_label' => 'Places',
        'threshold_label' => 'Threshold',

        'visible_label' => 'Visible',
        'display_chart_label' => 'Display Chart',
        'show_when_empty_label' => 'Show when empty',
    ],
    'overview' => [
        'metric_points_label' => 'Metric Points',
        'metric_points_description' => 'Recent metric points.',
    ],
    'sum_label' => 'Sum',
    'average_label' => 'Average',
    'view_labels' => [
        'last_hour' => 'Last Hour',
        'today' => 'Today',
        'week' => 'Week',
        'month' => 'Month',
    ],
];
