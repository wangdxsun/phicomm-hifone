<?php

return [
    'title'   => trans('administrator::dashboard.users.users'),
    'single'  => trans('administrator::dashboard.users.users'),
    'model'=>'Hifone\Models\User',
    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'avatar_url' => [
            'title'  => trans('administrator::dashboard.users.avatar'),
            'output' => function ($value) {
                return empty($value) ? 'N/A' : <<<EOD
    <img src="$value" width="80">
EOD;
            },
            'sortable' => false,
        ],
        'username' => [
            'title'    => trans('administrator::dashboard.users.username'),
            'sortable' => false,
        ],
        'nickname' => [
            'title'    => trans('administrator::dashboard.users.nickname'),
            'sortable' => false,
        ],
        'email' => [
            'title' => trans('administrator::dashboard.users.email'),
        ],
        'is_banned' => [
            'title' => trans('administrator::dashboard.users.is_banned'),
        ],
        'operation' => [
            'title'  => trans('administrator::administrator.operation'),
            'output' => function ($value, $model) {
                return $value;
            },
            'sortable' => false,
        ],
    ],
    'edit_fields' => [
        'username' => [
            'title' => trans('administrator::dashboard.users.username'),
        ],
        'email' => [
            'title' => trans('administrator::dashboard.users.email'),
        ],
        'is_banned' => [
            'title'    => trans('administrator::dashboard.users.is_banned'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
        ],
        'avatar_url' => [
            'title' => trans('administrator::dashboard.users.avatar')
        ],
        'location' => [
            'title' => '所处城市'
        ],
        'company' => [
            'title' => '所处公司'
        ],
        'website' => [
            'title' => '个人网站'
        ],
        'signature' => [
            'title' => '个性签名'
        ],
        'nickname' => [
            'title' => trans('administrator::dashboard.users.nickname')
        ],
        'roles' => array(
            'type'       => 'relationship',
            'title'      => trans('administrator::dashboard.roles.roles'),
            'name_field' => 'display_name',
        ),
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'username' => [
            'title' => trans('administrator::dashboard.users.username'),
        ],
        'nickname' => [
            'title' => trans('administrator::dashboard.users.nickname')
        ],
        'email' => [
            'title' => trans('administrator::dashboard.users.email'),
        ],
        'roles' => [
            'type'       => 'relationship',
            'title'      => trans('administrator::dashboard.roles.roles'),
            'name_field' => 'display_name',
        ],
        'is_banned' => [
            'title'    => trans('administrator::dashboard.users.is_banned'),
            'type'     => 'enum',
            'options'  => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
        ],
    ],
    'actions' => [],
];