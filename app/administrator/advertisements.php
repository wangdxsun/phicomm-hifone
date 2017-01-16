<?php

return [
    'title'   => trans('administrator::dashboard.advertisements.advertisements'),
    'single'  => trans('administrator::dashboard.advertisements.advertisements'),
    'model'   => 'Hifone\Models\Advertisement',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.advertisements.name'),
            'sortable' => false,
        ],
        'body' => [
            'title'    => trans('administrator::dashboard.advertisements.body'),
            'sortable' => false,
        ],
        'adspace' => [
            'title'    => trans('administrator::dashboard.advertisements.adspace'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->adspace->name,
                    'adspace',
                    $model->adspace_id
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
        'name' => [
            'title'    => trans('administrator::dashboard.advertisements.name'),
        ],
        'body' => [
            'title'    => trans('administrator::dashboard.advertisements.body'),
            'type'     => 'textarea',
        ],
        'adspace' => [
            'title'              => trans('administrator::dashboard.advertisements.adspace'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', position)"),
            'options_sort_field' => 'id',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.advertisements.name'),
        ],
        'adspace' => [
            'title'              => trans('administrator::dashboard.advertisements.adspace'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', position)"),
            'options_sort_field' => 'id',
        ],
    ],
];