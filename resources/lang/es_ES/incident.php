<?php

return [
    'title' => 'Incidentes',
    'resource_label' => 'Incidente|Incidentes',
    'status' => [
        'investigating' => 'Investigando',
        'identified' => 'Identificado',
        'watching' => 'Monitoreando',
        'fixed' => 'Solucionado',
        'reported' => 'Reportado',
    ],
    'edit_button' => 'Editar Incidente',
    'new_button' => 'Nuevo Incidente',
    'no_incidents_reported' => 'No se han reportado incidentes.',
    'timeline' => [
        'past_incidents_header' => 'Incidentes Pasados',
        'recent_incidents_header' => 'Incidentes Recientes',
        'no_incidents_reported_between' => 'No se han reportado incidentes entre :from y :to',
        'navigate' => [
            'previous' => 'Anterior',
            'today' => 'Hoy',
            'next' => 'Siguiente',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'status' => 'Estado',
            'visible' => 'Visible',
            'stickied' => 'Fijado',
            'occurred_at' => 'Ocurrido el',
            'notified_subscribers' => 'Suscriptores notificados',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
            'deleted_at' => 'Eliminado el',
        ],
        'actions' => [
            'record_update' => 'Registrar actualización',
            'view_incident' => 'Ver incidente',
        ],
        'empty_state' => [
            'heading' => 'Incidentes',
            'description' => 'Los incidentes se utilizan para comunicar y rastrear el estado de tus servicios.',
        ],
    ],
    'form' => [
        'name_label' => 'Nombre',
        'status_label' => 'Estado',
        'message_label' => 'Mensaje',
        'occurred_at_label' => 'Ocurrido el',
        'occurred_at_helper' => 'Se usará la fecha de creación del incidente si se deja vacío.',
        'visible_label' => 'Visible',
        'user_label' => 'Usuario',
        'user_helper' => 'El usuario que reportó el incidente.',
        'notifications_label' => '¿Notificar a los suscriptores?',
        'stickied_label' => '¿Incidente fijado?',
        'guid_label' => 'UUID del Incidente',
        'add_component' => [
            'action_label' => 'Añadir Componente',
            'header' => 'Componentes',
            'component_label' => 'Componente',
            'status_label' => 'Estado',
        ],
    ],
    'record_update' => [
        'success_title' => 'Incidente :name Actualizado',
        'success_body' => 'Se ha registrado una nueva actualización del incidente.',
        'form' => [
            'message_label' => 'Mensaje',
            'status_label' => 'Estado',
            'user_label' => 'Usuario',
            'user_helper' => 'Quién reportó este incidente.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Total de Incidentes',
        'total_incidents_description' => 'Número total de incidentes.',
    ],
];
