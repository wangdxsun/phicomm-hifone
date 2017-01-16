<?php

return [
    'title'   => trans('administrator::dashboard.threads.threads'),
    'single'  => trans('administrator::dashboard.threads.threads'),
    'model'   => 'Hifone\Models\Thread',

    'columns' => [

        'id' => [
            'title' => 'ID',
        ],
        'title' => [
            'title'    => trans('administrator::dashboard.threads.title'),
            'sortable' => false,
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.threads.order'),
        ],
        'user' => [
            'title'    => trans('administrator::dashboard.users.username'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->user->username,
                    'users',
                    $model->user_id
                );
            },
        ],
        'node' => [
            'title'    => trans('administrator::dashboard.threads.node'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->node->name,
                    'nodes',
                    $model->node_id
                );
            },
        ],
        'is_excellent' => [
            'title'    => trans('administrator::dashboard.threads.is_excellent'),
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.threads.is_blocked'),
        ],
        'reply_count' => [
            'title'    => trans('administrator::dashboard.threads.reply_count'),
        ],
        'view_count' => [
            'title'    => trans('administrator::dashboard.threads.view_count'),
        ],
        'favorite_count' => [
            'title'    => trans('administrator::dashboard.threads.favorite_count'),
        ],
        'like_count' => [
            'title'    => trans('administrator::dashboard.threads.like_count'),
        ],

        'operation' => [
            'title'  => trans('administrator::administrator.operation'),
            'output' => function ($value, $model) {

            },
            'sortable' => false,
        ],
    ],
    'edit_fields' => [
        'title' => [
            'title'    => trans('administrator::dashboard.threads.title'),
            'sortable' => false,
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.users.username'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'node' => [
            'title'              => trans('administrator::dashboard.threads.node'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'search_fields'      => array("CONCAT(id, ' ', name)"),
            'options_sort_field' => 'id',
        ],
        'body_original' => [
            'title'    => 'Markdown content',
            'hint'     => 'support Markdown',
            'type'     => 'textarea',
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.threads.order'),
        ],
        'is_excellent' => [
            'title'    => trans('administrator::dashboard.threads.is_excellent'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value' => 'no',
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.threads.is_blocked'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value' => 'no',
        ],
        'reply_count' => [
            'title'    => trans('administrator::dashboard.threads.reply_count'),
        ],
        'view_count' => [
            'title'    => trans('administrator::dashboard.threads.view_count'),
        ],
        'favorite_count' => [
            'title'    => trans('administrator::dashboard.threads.favorite_count'),
        ],
        'like_count' => [
            'title'    => trans('administrator::dashboard.threads.like_count'),
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.users.username'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'node' => [
            'title'              => trans('administrator::dashboard.threads.node'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'search_fields'      => array("CONCAT(id, ' ', screen_name)"),
            'options_sort_field' => 'id',
        ],
        'body_original' => [
            'title'    => 'Markdown content',
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.threads.order'),
        ],
        'is_excellent' => [
            'title'    => trans('administrator::dashboard.threads.is_excellent'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value' => 'no',
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.threads.is_blocked'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value' => 'no',
        ],
        'view_count' => [
            'type'                => 'number',
            'title'               => trans('administrator::dashboard.threads.view_count'),
            'thousands_separator' => ',', //optional, defaults to ','
            'decimal_separator'   => '.',   //optional, defaults to '.'
        ],
    ],
    'rules'   => [
        'title' => 'required'
    ],
    'messages' => [
        'title.required' => 'title is required.',
    ],
];