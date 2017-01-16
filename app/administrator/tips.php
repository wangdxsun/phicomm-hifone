<?php

return [
    'title'   => trans('administrator::dashboard.tips.tips'),
    'single'  => trans('administrator::dashboard.tips.tips'),
    'model'   => 'Hifone\Models\Tip',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'body' => [
            'title'    => trans('administrator::dashboard.tips.body'),
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
        'body' => [
            'title' => trans('administrator::dashboard.tips.body'),
            'type'  => 'textarea',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'body' => [
            'title' => trans('administrator::dashboard.tips.body'),
        ],
    ],
];