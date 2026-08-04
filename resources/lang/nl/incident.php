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
            'pinned' => 'Bovenaan vastpinnen',
            'occurred_at' => 'Voorgekomen op',
            'published_at' => 'Gepubliceerd op',
            'notified_subscribers' => 'Geïnformeerde abonnees',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
        ],
        'published_now' => 'Gepubliceerd',
        'filters' => [
            'scheduled' => 'Gepland (nog niet gepubliceerd)',
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
        'published_at_label' => 'Gepubliceerd op',
        'published_at_helper' => 'Plan het incident om op een toekomstige datum op de statuspagina te verschijnen. Tot dan verborgen voor iedereen. Laat leeg om direct te publiceren.',
        'visible_label' => 'Zichtbaar',
        'user_label' => 'Gebruiker',
        'user_helper' => 'Gebruiker die het incident heeft gemeld.',
        'notifications_label' => 'Abonnees op de hoogte stellen?',
        'pin_incident_label' => 'Het incident bovenaan de statuspagina vastpinnen.',
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
