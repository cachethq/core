<?php

return [
    'resource_label' => '计划|计划',
    'list' => [
        'headers' => [
            'name' => '名称',
            'status' => '状态',
            'scheduled_at' => '预定时间',
            'completed_at' => '完成时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ],
        'empty_state' => [
            'heading' => '计划安排',
            'description' => '规划和安排你的维护工作。',
        ],
        'actions' => [
            'record_update' => '记录更新',
            'complete' => '完成维护',
        ],
    ],
    'form' => [
        'name_label' => '名称',
        'message_label' => '消息',
        'scheduled_at_label' => '预定时间',
        'completed_at_label' => '完成时间',
    ],
    'add_update' => [
        'success_title' => '计划 :name 已更新',
        'success_body' => '已记录新的计划更新。',
        'form' => [
            'message_label' => '消息',
            'completed_at_label' => '完成时间',
        ],
    ],
    'status' => [
        'upcoming' => '即将进行',
        'in_progress' => '进行中',
        'complete' => '完成',
    ],
    'planned_maintenance_header' => '计划维护',
];
