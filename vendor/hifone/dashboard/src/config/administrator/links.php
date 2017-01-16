<?php

return [
    'title'   => trans('administrator::dashboard.links.links'),
    'single'  => trans('administrator::dashboard.links.links'),
    'model'   => 'Hifone\Models\Link',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'title' => [
            'title'    => trans('administrator::dashboard.links.title'),
            'sortable' => false,
        ],
        'url' => [
            'title'    => trans('administrator::dashboard.links.url'),
            'sortable' => false,
        ],
        'cover' => [
            'title'    => trans('administrator::dashboard.links.cover'),
            'output' => function ($value) {
                return empty($value) ? 'N/A' : <<<EOD
    <img src="$value" width="180">
EOD;
            },
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
        'title' => [
            'title'    => trans('administrator::dashboard.links.title'),
        ],
        'url' => [
            'title'    => trans('administrator::dashboard.links.url'),
        ],
        'cover' => [
            'title'    => trans('administrator::dashboard.links.cover'),
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'title' => [
            'title' => trans('administrator::dashboard.links.title'),
        ],
    ],
];