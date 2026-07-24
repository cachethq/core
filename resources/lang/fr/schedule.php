<?php

return [
    'resource_label' => 'Planification|Planifications',
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'scheduled_at' => 'Planifié le',
            'completed_at' => 'Terminé le',
            'published_at' => 'Publié le',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
            'deleted_at' => 'Supprimé le',
        ],
        'published_now' => 'Publié',
        'filters' => [
            'scheduled' => 'Programmé (pas encore publié)',
        ],
        'empty_state' => [
            'heading' => 'Planifications',
            'description' => 'Planifiez et programmez votre maintenance.',
        ],
        'actions' => [
            'record_update' => 'Enregistrer une mise à jour',
            'complete' => 'Terminer la maintenance',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'message_label' => 'Message',
        'scheduled_at_label' => 'Planifié le',
        'completed_at_label' => 'Terminé le',
        'published_at_label' => 'Publié le',
        'published_at_helper' => 'Programmez la maintenance pour qu’elle apparaisse sur la page d’état à une date future. Masquée à tous jusqu’à cette date. Laissez vide pour publier immédiatement.',
    ],
    'add_update' => [
        'success_title' => 'Planification :name mise à jour',
        'success_body' => 'Une nouvelle mise à jour de la planification a été enregistrée.',
        'form' => [
            'message_label' => 'Message',
            'completed_at_label' => 'Terminé le',
        ],
    ],
    'status' => [
        'upcoming' => 'À venir',
        'in_progress' => 'En cours',
        'complete' => 'Terminé',
    ],
    'planned_maintenance_header' => 'Maintenance planifiée',
];
