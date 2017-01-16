<?php

return [
    'title'   => trans('administrator::dashboard.replies.replies'),
    'single'  => trans('administrator::dashboard.replies.replies'),
    'model'   => 'Hifone\Models\Reply',

    'columns' => [

        'id' => [
            'title' => 'ID',
        ],
        'user' => [
            'title'    => trans('administrator::dashboard.replies.author'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->user->username,
                    'users',
                    $model->user_id
                );
            },
        ],
        'thread' => [
            'title'    => trans('administrator::dashboard.replies.thread'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->thread->title,
                    'threads',
                    $model->thread_id
                );
            },
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.replies.is_blocked'),
        ],
        'like_count' => [
            'title'    => trans('administrator::dashboard.replies.like_count'),
        ],
        'operation' => [
            'title'  => trans('administrator::administrator.operation'),
            'output' => function ($value, $model) {

            },
            'sortable' => false,
        ],
    ],
    'edit_fields' => [
        'user' => [
            'title'              => trans('administrator::dashboard.replies.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'thread' => [
            'title'              => trans('administrator::dashboard.replies.thread'),
            'type'               => 'relationship',
            'name_field'         => 'title',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', title)"),
            'options_sort_field' => 'id',
        ],
        'body_original' => [
            'title'    => 'Markdown body',
            'hint'     => 'support Markdown',
            'type'     => 'textarea',
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.replies.is_blocked'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value' => 'no',
        ],
        'like_count' => [
            'title'    => trans('administrator::dashboard.replies.like_count'),
        ],
    ],
    'filters' => [
        'user' => [
            'title'              => trans('administrator::dashboard.replies.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'thread' => [
            'title'              => trans('administrator::dashboard.replies.thread'),
            'type'               => 'relationship',
            'name_field'         => 'title',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', title)"),
            'options_sort_field' => 'id',
        ],
        'is_blocked' => [
            'title'    => trans('administrator::dashboard.replies.is_blocked'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
        ],
        'body_original' => [
            'title'    => trans('administrator::dashboard.replies.body_original'),
        ],
        'like_count' => [
            'type'                => 'number',
            'title'               => trans('administrator::dashboard.replies.like_count'),
            'thousands_separator' => ',', //optional, defaults to ','
            'decimal_separator'   => '.',   //optional, defaults to '.'
        ],
    ],
    'rules'   => [
        'body_original' => 'required'
    ],
    'messages' => [
        'body_original.required' => 'body_original is required.',
    ],
];