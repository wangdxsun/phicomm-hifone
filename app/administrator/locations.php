<?php

return [
    'title'   => trans('administrator::dashboard.locations.locations'),
    'single'  => trans('administrator::dashboard.locations.locations'),
    'model'   => 'Hifone\Models\Location',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.locations.name'),
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
            'title' => trans('administrator::dashboard.locations.name'),
            'type'  => 'text',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.locations.name'),
        ],
    ],
];