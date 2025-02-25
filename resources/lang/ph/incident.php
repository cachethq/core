<?php

return [
    'title' => 'Mga Insidente',
    'resource_label' => 'Insidente|Mga Insidente',
    'status' => [
        'investigating' => 'Iniimbestigahan',
        'identified' => 'Natukoy',
        'watching' => 'Binabantayan',
        'fixed' => 'Naayos',
        'reported' => 'Na-ulat',
    ],
    'edit_button' => 'I-edit ang Insidente',
    'new_button' => 'Bagong Insidente',
    'no_incidents_reported' => 'Walang naiulat na insidente.',
    'timeline' => [
        'past_incidents_header' => 'Mga Nakaraang Insidente',
        'recent_incidents_header' => 'Mga Kamakailang Insidente',
        'no_incidents_reported_between' => 'Walang naiulat na insidente mula :from hanggang :to',
        'navigate' => [
            'previous' => 'Nakaraan',
            'today' => 'Ngayon',
            'next' => 'Susunod',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'Kalagayan',
            'visible' => 'Nakikita',
            'stickied' => 'Naka-sticky',
            'occurred_at' => 'Nangyari noong',
            'notified_subscribers' => 'Naabisuhan ang mga subscriber',
            'created_at' => 'Ginawa noong',
            'updated_at' => 'Na-update noong',
            'deleted_at' => 'Nabura noong',
        ],
        'actions' => [
            'record_update' => 'I-record ang Update',
            'view_incident' => 'Tingnan ang Insidente',
        ],
        'empty_state' => [
            'heading' => 'Mga Insidente',
            'description' => 'Ang mga insidente ay ginagamit para ipaalam at subaybayan ang kalagayan ng iyong mga serbisyo.',
        ],
    ],
    'form' => [
        'name_label' => 'Pangalan',
        'status_label' => 'Kalagayan',
        'message_label' => 'Mensahe',
        'occurred_at_label' => 'Nangyari noong',
        'occurred_at_helper' => 'Gagamitin ang timestamp ng paglikha ng insidente kung ito ay iiwanang walang laman.',
        'visible_label' => 'Nakikita',
        'user_label' => 'Gumagamit',
        'user_helper' => 'Ang gumagamit na nag-ulat ng insidente.',
        'notifications_label' => 'Abisuhan ang mga Subscriber?',
        'stickied_label' => 'Gawing Sticky ang Insidente?',
        'guid_label' => 'Incident UUID',
        'add_component' => [
            'action_label' => 'Magdagdag ng Komponent',
            'header' => 'Mga Komponent',
            'component_label' => 'Komponent',
            'status_label' => 'Kalagayan',
        ],
    ],
    'record_update' => [
        'success_title' => 'Na-update ang Insidente :name',
        'success_body' => 'Isang bagong update ng insidente ang naitala.',
        'form' => [
            'message_label' => 'Mensahe',
            'status_label' => 'Kalagayan',
            'user_label' => 'Gumagamit',
            'user_helper' => 'Sino ang nag-ulat ng insidenteng ito.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Kabuuang Insidente',
        'total_incidents_description' => 'Kabuuang bilang ng mga insidente.',
    ],
];
