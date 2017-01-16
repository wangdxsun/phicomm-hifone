<?php

return [
    'title'   => trans('administrator::dashboard.providers.providers'),
    'single'  => trans('administrator::dashboard.providers.providers'),
    'model'   => 'Hifone\Models\Provider',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.providers.name'),
            'sortable' => false,
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.providers.slug'),
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
            'title' => trans('administrator::dashboard.providers.name'),
            'type'  => 'text',
        ],
        'slug' => [
            'title' => trans('administrator::dashboard.providers.slug'),
            'type'  => 'text',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.providers.name'),
        ],
        'slug' => [
            'title' => trans('administrator::dashboard.providers.slug'),
        ],
    ],
];