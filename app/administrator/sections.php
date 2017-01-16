<?php

return [
    'title'   => trans('administrator::dashboard.sections.sections'),
    'single'  => trans('administrator::dashboard.sections.sections'),
    'model'   => 'Hifone\Models\Section',

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.sections.name'),
            'sortable' => false,
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.sections.order'),
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
            'title' => trans('administrator::dashboard.sections.name'),
        ],
        'order' => [
            'title'    => trans('administrator::dashboard.sections.order'),
        ],
    ],
    'filters' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title' => trans('administrator::dashboard.sections.name'),
        ],
    ],
    'rules'   => [
        'name' => 'required|min:1|unique:sections'
    ],
    'messages' => [
        'name.unique'   => '分类名在数据库里有重复，请选用其他名称。',
        'name.required' => '请确保名字至少一个字符以上',
    ],
];