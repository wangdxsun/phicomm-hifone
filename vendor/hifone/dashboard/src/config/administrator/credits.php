<?php

return [
    'title'   => trans('administrator::dashboard.credits.credits'),
    'single'  => trans('administrator::dashboard.credits.credits'),
    'model'   => 'Hifone\Models\Credit',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'balance' => [
            'title'    => trans('administrator::dashboard.credits.balance'),
            'sortable' => true,
        ],
        'frequency_tag' => [
            'title'    => trans('administrator::dashboard.credits.frequency_tag'),
            'sortable' => false,
        ],
        'body' => [
            'title'    => trans('administrator::dashboard.credits.body'),
            'sortable' => false,
        ],
        'rule' => [
            'title'    => trans('administrator::dashboard.credits.credit_rule'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->rule->name . ' (' . $model->rule->slug . ')',
                    'credit_rules',
                    $model->rule_id
                );
            },
        ],
        'user' => [
            'title'    => trans('administrator::dashboard.credits.author'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->user->username,
                    'users',
                    $model->user_id
                );
            },
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
        'balance' => [
            'title' => trans('administrator::dashboard.credits.balance'),
            'type'  => 'textarea',
        ],
        'frequency_tag' => [
            'title' => trans('administrator::dashboard.credits.frequency_tag'),
            'type'  => 'text',
        ],
        'body' => [
            'title' => trans('administrator::dashboard.credits.body'),
            'type'  => 'textarea',
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.credits.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'rule' => [
            'title'              => trans('administrator::dashboard.credits.credit_rule'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', slug)"),
            'options_sort_field' => 'id',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'balance' => [
            'title' => trans('administrator::dashboard.credits.balance'),
        ],
        'frequency_tag' => [
            'title' => trans('administrator::dashboard.credits.frequency_tag'),
        ],
        'body' => [
            'title' => trans('administrator::dashboard.credits.body'),
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.credits.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'rule' => [
            'title'              => trans('administrator::dashboard.credits.credit_rule'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', slug)"),
            'options_sort_field' => 'id',
        ],
    ],
];