<?php

return [
    'title' => 'Incidents',
    'resource_label' => 'Incident|Incidents',
    'status' => [
        'investigating' => 'En cours d’investigation',
        'identified' => 'Problème identifié',
        'watching' => 'En surveillance',
        'fixed' => 'Résolu',
        'reported' => 'Signalé',
    ],
    'edit_button' => 'Modifier l’incident',
    'new_button' => 'Nouvel incident',
    'no_incidents_reported' => 'Aucun incident signalé.',
    'timeline' => [
        'past_incidents_header' => 'Incidents passés',
        'recent_incidents_header' => 'Incidents récents',
        'no_incidents_reported_between' => 'Aucun incident signalé entre :from et :to',
        'navigate' => [
            'previous' => 'Précédent',
            'today' => 'Aujourd’hui',
            'next' => 'Suivant',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'visible' => 'Visible',
            'stickied' => 'Épinglé',
            'occurred_at' => 'Survenu le',
            'notified_subscribers' => 'Abonnés notifiés',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
            'deleted_at' => 'Supprimé le',
        ],
        'actions' => [
            'record_update' => 'Enregistrer une mise à jour',
            'view_incident' => 'Voir l’incident',
        ],
        'empty_state' => [
            'heading' => 'Incidents',
            'description' => 'Les incidents servent à communiquer et suivre l’état de vos services.',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'status_label' => 'Statut',
        'message_label' => 'Message',
        'occurred_at_label' => 'Survenu le',
        'occurred_at_helper' => 'L’horodatage de création de l’incident sera utilisé si laissé vide.',
        'visible_label' => 'Visible',
        'user_label' => 'Utilisateur',
        'user_helper' => 'L’utilisateur ayant signalé l’incident.',
        'notifications_label' => 'Notifier les abonnés ?',
        'stickied_label' => 'Incident épinglé ?',
        'guid_label' => 'UUID de l’incident',
        'add_component' => [
            'action_label' => 'Ajouter un composant',
            'header' => 'Composants',
            'component_label' => 'Composant',
            'status_label' => 'Statut',
        ],
    ],
    'record_update' => [
        'success_title' => 'Incident :name mis à jour',
        'success_body' => 'Une nouvelle mise à jour d’incident a été enregistrée.',
        'form' => [
            'message_label' => 'Message',
            'status_label' => 'Statut',
            'user_label' => 'Utilisateur',
            'user_helper' => 'Qui a signalé cet incident.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Nombre total d’incidents',
        'total_incidents_description' => 'Nombre total d’incidents signalés.',
    ],
];
