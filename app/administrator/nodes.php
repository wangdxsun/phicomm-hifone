<?php

return [
    'title'   => trans('administrator::dashboard.nodes.nodes'),
    'single'  => trans('administrator::dashboard.nodes.nodes'),
    'model'   => 'Hifone\Models\Node',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.nodes.name'),
            'sortable' => false,
        ],
        'slug' => [
            'title'    => 'Slug',
            'sortable' => false,
        ],
        'description' => [
            'title'    => trans('administrator::dashboard.nodes.description'),
            'sortable' => false,
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.nodes.order'),
            'sortable' => true,
        ],
        'section' => [
            'title'    => trans('administrator::dashboard.nodes.parent'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->section->name,
                    'sections',
                    $model->section_id
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
            'title' => trans('administrator::dashboard.nodes.name'),
        ],
        'slug' => [
            'title' => 'Slug',
        ],
        'description' => [
            'title' => trans('administrator::dashboard.nodes.description'),
            'type' => 'textarea',
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.nodes.order'),
        ],
        'section' => [
            'title'              => trans('administrator::dashboard.nodes.parent'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'search_fields'      => array("CONCAT(id, ' ', name)"),
            'options_sort_field' => 'id',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.nodes.name'),
        ],
        'slug' => [
            'title' => 'Slug',
        ],
        'description' => [
            'title' => trans('administrator::dashboard.nodes.description'),
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.nodes.order'),
        ],
        'section' => [
            'title'              => trans('administrator::dashboard.nodes.parent'),
            'type'               => 'relationship',
            'name_field'         => 'name',
            'search_fields'      => array("CONCAT(id, ' ', name)"),
            'options_sort_field' => 'id',
        ],
    ],
    'rules'   => [
        'name' => 'required|min:1|unique:nodes'
    ],
    'messages' => [
        'name.unique'   => '分类名在数据库里有重复，请选用其他名称。',
        'name.required' => '请确保名字至少一个字符以上',
    ],
];