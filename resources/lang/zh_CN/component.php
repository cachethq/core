<?php

return [
    'resource_label' => '组件|组件',
    'list' => [
        'headers' => [
            'name' => '名称',
            'status' => '状态',
            'order' => '顺序',
            'group' => '组',
            'enabled' => '启用',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ],
        'empty_state' => [
            'heading' => '组件',
            'description' => '组件代表系统中可能影响状态页面状态的各种部分。',
        ],
    ],
    'last_updated' => '上次更新 :timestamp',
    'view_details' => '查看详情',
    'form' => [
        'name_label' => '名称',
        'status_label' => '状态',
        'description_label' => '描述',
        'component_group_label' => '组件组',
        'link_label' => '链接',
        'link_helper' => '可选的组件链接。',
    ],
    'status' => [
        'operational' => '正常运行',
        'performance_issues' => '性能问题',
        'partial_outage' => '部分故障',
        'major_outage' => '重大故障',
        'unknown' => '未知',
    ],

];
