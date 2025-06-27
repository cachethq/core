<?php

return [
    'resource_label' => 'Composant|Composants',
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'order' => 'Ordre',
            'group' => 'Groupe',
            'enabled' => 'Activé',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
            'deleted_at' => 'Supprimé le',
        ],
        'empty_state' => [
            'heading' => 'Composants',
            'description' => 'Les composants représentent les différentes parties de votre système qui peuvent affecter l’état de votre page de statut.',
        ],
    ],
    'last_updated' => 'Dernière mise à jour :timestamp',
    'view_details' => 'Voir les détails',
    'form' => [
        'name_label' => 'Nom',
        'status_label' => 'Statut',
        'description_label' => 'Description',
        'component_group_label' => 'Groupe de composants',
        'link_label' => 'Lien',
        'link_helper' => 'Un lien optionnel vers le composant.',
    ],
    'status' => [
        'operational' => 'Opérationnel',
        'performance_issues' => 'Problèmes de performance',
        'partial_outage' => 'Panne partielle',
        'major_outage' => 'Panne majeure',
        'under_maintenance' => 'En maintenance',
        'unknown' => 'Inconnu',
    ],
];
