<?php

return [
    'resource_label' => 'Subscritor|Subscritores',
    'list' => [
        'headers' => [
            'email' => 'E-mail',
            'phone_number' => 'Número de telefone',
            'slack_webhook_url' => 'URL do webhook do Slack',
            'verified_at' => 'Verificado em',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Subscritores',
            'description' => 'Os subscritores são as pessoas que subscreveram a sua página de estado para receber notificações.',
        ],
        'actions' => [
            'verify_label' => 'Verificar',
            'resend_verification_label' => 'Reenviar verificação',
        ],
    ],
    'resend_verification' => [
        'success_title' => 'E-mail de verificação enviado',
        'success_body' => 'Foi enviado um novo e-mail de verificação para :email.',
    ],
    'form' => [
        'email_label' => 'E-mail',
        'verified_at_label' => 'Verificado em',
        'meta_label' => 'Metadados',
    ],
    'overview' => [
        'total_subscribers_label' => 'Total de subscritores',
        'verified_subscribers_description' => ':count verificados',
    ],
    'status_page' => [
        'subscribe' => [
            'title' => 'Subscrever atualizações',
            'heading' => 'Subscrever atualizações',
            'description' => 'Receba um e-mail sempre que forem reportados incidentes ou agendadas manutenções.',
            'consent' => 'Vamos enviar-lhe um e-mail de confirmação. Pode cancelar a subscrição a qualquer momento.',
            'back' => 'Voltar à página de estado',
            'button_label' => 'Subscrever',
            'subscribed_heading' => 'Consulte o seu e-mail',
            'subscribed_body' => 'Se este endereço de e-mail ainda não estiver subscrito, vai receber um e-mail com uma ligação para confirmar a subscrição.',
            'verified_heading' => 'Subscrição confirmada',
            'verified_body' => 'A partir de agora, vai receber as atualizações de estado por e-mail.',
            'unsubscribed_heading' => 'A sua subscrição foi cancelada',
            'unsubscribed_body' => 'Deixará de receber atualizações de estado por e-mail. Pode voltar a subscrever a qualquer momento.',
        ],
        'unsubscribe' => [
            'title' => 'Cancelar subscrição',
            'heading' => 'Cancelar a subscrição das atualizações?',
            'body' => ':email deixará de receber atualizações de estado.',
            'button' => 'Cancelar subscrição',
            'cancel' => 'Manter a subscrição',
        ],
        'email_label' => 'Endereço de e-mail',
        'email_placeholder' => 'nome@exemplo.pt',
        'subscribe_button' => 'Subscrever',
    ],
    'mail' => [
        'unsubscribe' => 'Cancelar subscrição',
        'new_incident' => [
            'subject' => 'Novo incidente: :incident',
            'button' => 'Ver incidente',
        ],
        'incident_updated' => [
            'subject' => 'Incidente atualizado: :incident',
            'button' => 'Ver incidente',
        ],
        'new_schedule' => [
            'subject' => 'Manutenção agendada: :schedule',
            'scheduled_for' => 'Agendada para',
            'button' => 'Ver manutenção',
        ],
        'schedule_updated' => [
            'subject' => 'Manutenção atualizada: :schedule',
            'updated_at' => 'Atualizada',
            'button' => 'Ver manutenção',
        ],
        'schedule_rescheduled' => [
            'subject' => 'Manutenção reagendada: :schedule',
            'previously' => 'Anteriormente',
            'now' => 'Agora agendada para',
            'button' => 'Ver manutenção',
        ],
        'schedule_completed' => [
            'subject' => 'Manutenção concluída: :schedule',
            'completed_at' => 'Concluída',
            'body' => 'A manutenção agendada foi concluída e todos os serviços afetados devem estar agora a funcionar normalmente.',
            'button' => 'Ver manutenção',
        ],
        'verify' => [
            'subject' => 'Confirme a sua subscrição',
            'heading' => 'Confirme a sua subscrição',
            'body' => 'Está a receber este e-mail porque alguém subscreveu as atualizações de estado de :app. Confirme a subscrição para começar a receber atualizações.',
            'button' => 'Confirmar subscrição',
            'ignore' => 'Se não subscreveu :app, pode ignorar este e-mail em segurança.',
        ],
    ],
];
