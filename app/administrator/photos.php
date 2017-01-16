<?php

return [
    'title'   => trans('administrator::dashboard.photos.photos'),
    'single'  => trans('administrator::dashboard.photos.photos'),
    'model'   => 'Hifone\Models\Photo',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'image' => [
            'title'    => trans('administrator::dashboard.photos.image'),
            'output' => function ($value) {
                return empty($value) ? 'N/A' : <<<EOD
    <img src="$value" width="80">
EOD;
            },
            'sortable' => false,
        ],
        'user' => [
            'title'    => trans('administrator::dashboard.photos.author'),
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
        'image' => [
            'title' => trans('administrator::dashboard.photos.image'),
            'type'  => 'textarea',
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.photos.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'image' => [
            'title' => trans('administrator::dashboard.photos.image'),
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.photos.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
    ],
];