<?php

return [
    'title' => 'Incidenten',
    'resource_label' => 'Incident|Incidenten',
    'status' => [
        'investigating' => 'Onderzoek',
        'identified' => 'Geïdentificeerd',
        'watching' => 'Observatie',
        'fixed' => 'Opgelost',
        'reported' => 'Gerapporteerd',
    ],
    'edit_button' => 'Incident bewerken',
    'new_button' => 'Incident toevoegen',
    'no_incidents_reported' => 'Er zijn geen incidenten gemeld.',
    'affected_components_header' => 'Getroffen componenten',
    'timeline' => [
        'past_incidents_header' => 'Eerdere incidenten',
        'recent_incidents_header' => 'Recente incidenten',
        'no_incidents_reported_between' => 'Geen incidenten gemeld tussen :from en :to',
        'navigate' => [
            'previous' => 'Achteruit',
            'today' => 'Vandaag',
            'next' => 'Vooruit',
            'timeline' => 'Terug naar tijdlijn',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'status' => 'Toestand',
            'visible' => 'Zichtbaar',
            'stickied' => 'Vastgepind',
            'occurred_at' => 'Voorgekomen op',
            'notified_subscribers' => 'Geïnformeerde abonnees',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
        ],
        'actions' => [
            'record_update' => 'Update publiceren',
            'view_incident' => 'Bekijk incident',
        ],
        'empty_state' => [
            'heading' => 'Incidenten',
            'description' => 'Incidenten worden gebruikt om de status van uw diensten te communiceren en te volgen.',
        ],
    ],
    'form' => [
        'name_label' => 'Naam',
        'status_label' => 'Toestand',
        'message_label' => 'Nieuws',
        'occurred_at_label' => 'Voorgekomen op',
        'occurred_at_helper' => 'Als dit veld leeg wordt gelaten, wordt het tijdstempel gebruikt waarop het incident is ontstaan.',
        'visible_label' => 'Zichtbaar',
        'user_label' => 'Gebruiker',
        'user_helper' => 'Gebruiker die het incident heeft gemeld.',
        'notifications_label' => 'Abonnees op de hoogte stellen?',
        'stickied_label' => 'Pin-incident?',
        'guid_label' => 'Incident-UUID',
        'add_component' => [
            'action_label' => 'Component toevoegen',
            'header' => 'Componenten',
            'component_label' => 'Onderdeel',
            'status_label' => 'Toestand',
        ],
    ],
    'record_update' => [
        'success_title' => 'Incident bijgewerkt - :name',
        'success_body' => 'Er is een update over het incident vrijgegeven.',
        'form' => [
            'message_label' => 'Nieuws',
            'status_label' => 'toestand',
            'user_label' => 'Gebruiker',
            'user_helper' => 'Wie heeft dit incident gemeld?',
        ],
    ],
    'overview' => [
        'open_incidents_label' => 'Openstaande incidenten',
        'open_incidents_description' => 'Incidenten die nog niet zijn opgelost.',
    ],
];
