<?php

return [
    'title'   => trans('administrator::dashboard.adblocks.adblocks'),
    'single'  => trans('administrator::dashboard.adblocks.adblocks'),
    'model'   => 'Hifone\Models\Ad\Adblock',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.adblocks.name'),
            'sortable' => false,
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.adblocks.slug'),
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
            'title'    => trans('administrator::dashboard.adblocks.name'),
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.adblocks.slug'),
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.adblocks.name'),
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.adblocks.slug'),
        ],
    ],
];