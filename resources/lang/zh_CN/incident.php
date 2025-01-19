<?php

return [
    'title' => '事件',
    'resource_label' => '事件|事件',
    'status' => [
        'investigating' => '正在调查',
        'identified' => '已确认',
        'watching' => '正在观察',
        'fixed' => '已修复',
        'reported' => '已报告',
    ],
    'edit_button' => '编辑事件',
    'new_button' => '新建事件',
    'no_incidents_reported' => '没有事件被报告。',
    'timeline' => [
        'past_incidents_header' => '过去的事件',
        'recent_incidents_header' => '最近的事件',
        'no_incidents_reported_between' => '在 :from 和 :to 之间没有事件被报告。',
        'navigate' => [
            'previous' => '上一页',
            'today' => '今天',
            'next' => '下一页',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => '名称',
            'status' => '状态',
            'visible' => '可见',
            'stickied' => '置顶',
            'occurred_at' => '发生时间',
            'notified_subscribers' => '已通知的订阅者',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ],
        'actions' => [
            'record_update' => '记录更新',
            'view_incident' => '查看事件',
        ],
        'empty_state' => [
            'heading' => '事件',
            'description' => '事件用于沟通和跟踪服务的状态。',
        ],
    ],
    'form' => [
        'name_label' => '名称',
        'status_label' => '状态',
        'message_label' => '消息',
        'occurred_at_label' => '发生时间',
        'occurred_at_helper' => '如果留空，将使用事件的创建时间戳。',
        'visible_label' => '可见',
        'user_label' => '用户',
        'user_helper' => '报告事件的用户。',
        'notifications_label' => '通知订阅者？',
        'stickied_label' => '置顶事件？',
        'guid_label' => '事件 UUID',
        'add_component' => [
            'action_label' => '添加组件',
            'header' => '组件',
            'component_label' => '组件',
            'status_label' => '状态',
        ],
    ],
    'record_update' => [
        'success_title' => '事件 :name 已更新',
        'success_body' => '一个新的事件更新已被记录。',
        'form' => [
            'message_label' => '消息',
            'status_label' => '状态',
            'user_label' => '用户',
            'user_helper' => '谁报告了此事件。',
        ],
    ],
    'overview' => [
        'total_incidents_label' => '总事件数',
        'total_incidents_description' => '事件的总数。',
    ],
];
