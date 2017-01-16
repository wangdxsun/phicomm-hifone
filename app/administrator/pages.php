<?php

return [
    'title'   => trans('administrator::dashboard.pages.pages'),
    'single'  => trans('administrator::dashboard.pages.pages'),
    'model'   => 'Hifone\Models\Page',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'title' => [
            'title'    => trans('administrator::dashboard.pages.title'),
            'sortable' => false,
        ],
        'slug' => [
            'title'    => trans('administrator::dashboard.pages.slug'),
            'sortable' => false,
        ],
        'body' => [
            'title'    => trans('administrator::dashboard.pages.body'),
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
            'title' => trans('administrator::dashboard.pages.title'),
        ],
        'slug' => [
            'title' => trans('administrator::dashboard.pages.slug'),
        ],
        'body' => [
            'title' => trans('administrator::dashboard.pages.body'),
            'type'  => 'textarea',
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'title' => [
            'title' => trans('administrator::dashboard.pages.title'),
        ],
        'slug' => [
            'title' => trans('administrator::dashboard.pages.slug'),
        ],
        'body' => [
            'title' => trans('administrator::dashboard.pages.body'),
        ],
    ],
    'rules'   => [
        'title' => 'required|min:1|unique:pages'
    ],
    'messages' => [
        'title.unique'   => '页面标题在数据库里有重复，请选用其他名称。',
        'title.required' => '请确保标题至少一个字符以上',
    ],
];