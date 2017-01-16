<?php

return [
    'title'   => trans('administrator::dashboard.adspaces.adspaces'),
    'single'  => trans('administrator::dashboard.adspaces.adspaces'),
    'model'   => 'Hifone\Models\Ad\Adspace',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.adspaces.name'),
            'sortable' => false,
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.adspaces.order'),
            'sortable' => true,
        ],
        'position' => [
            'title'    => trans('administrator::dashboard.adspaces.position'),
            'sortable' => false,
        ],
        'route' => [
            'title'    => trans('administrator::dashboard.adspaces.route'),
            'sortable' => false,
        ],
        'adblock' => [
            'title'    => trans('administrator::dashboard.adspaces.adblock'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->adblock->name,
                    'adblock',
                    $model->adblock_id
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
            'title'    => trans('administrator::dashboard.adspaces.name'),
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.adspaces.order'),
        ],
        'position' => [
            'title'    => trans('administrator::dashboard.adspaces.position'),
        ],
        'route' => [
            'title'    => trans('administrator::dashboard.adspaces.route'),
        ],
        'adblock' => [
            'title'              => trans('administrator::dashboard.adspaces.adblock'),
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
        'title' => [
            'title' => trans('administrator::dashboard.links.title'),
        ],
        'adblock' => [
            'title'              => trans('administrator::dashboard.adspaces.adblock'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', slug)"),
            'options_sort_field' => 'id',
        ],
    ],
];