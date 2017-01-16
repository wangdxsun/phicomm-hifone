<?php

return [
    'title'   => trans('administrator::dashboard.credit_rules.credit_rules'),
    'single'  => trans('administrator::dashboard.credit_rules.credit_rules'),
    'model'   => 'Hifone\Models\Credit\Rule',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.credit_rules.name'),
            'sortable' => false,
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.credit_rules.slug'),
            'sortable' => false,
        ],
        'frequency' => [
            'title'    => trans('administrator::dashboard.credit_rules.frequency'),
            'sortable' => false,
        ],
        'reward' => [
            'title'    => trans('administrator::dashboard.credit_rules.reward'),
            'sortable' => false,
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
        'name' => [
            'title' => trans('administrator::dashboard.credit_rules.name'),
        ],
        'slug' => [
            'title' => trans('administrator::dashboard.credit_rules.slug'),
        ],
        'frequency' => [
            'title' => trans('administrator::dashboard.credit_rules.frequency'),
        ],
        'reward' => [
            'title' => trans('administrator::dashboard.credit_rules.reward'),
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.credit_rules.name'),
        ],
    ],
    'messages' => [
        'name.unique'   => '分类名在数据库里有重复，请选用其他名称。',
        'name.required' => '请确保名字至少一个字符以上',
    ],
];