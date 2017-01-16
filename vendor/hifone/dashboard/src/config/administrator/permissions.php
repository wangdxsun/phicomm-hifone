<?php

return [
    'title'   => trans('administrator::dashboard.permissions.permissions'),
    'single'  => trans('administrator::dashboard.permissions.permissions'),
    'model'   => 'Hifone\Models\Permission',

    'permission' => function () {
        // return Auth::user()->hasRole('Developer');
        return true;
    },
    /*
    'action_permissions' => [
        'create' => function ($model) {
            return false;
        },
        'update' => function ($model) {
            return false;
        },
        'delete' => function ($model) {
            return false;
        },
        'view' => function ($model) {
            return true;
        },
    ],
    */

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'name' => [
            'title'    => trans('administrator::dashboard.permissions.name'),
            'sortable' => false,
        ],
        'display_name' => [
            'title'    => trans('administrator::dashboard.permissions.display_name'),
            'sortable' => false,
        ],
        'description' => [
            'title'    => trans('administrator::dashboard.permissions.description'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return empty($value) ? 'N/A' : $value;
            },
        ],
        'roles' => [
            'title'  => trans('administrator::dashboard.roles.roles'),
            'output' => function ($value, $model) {
                $model->load('roles');
                $result = [];
                foreach ($model->roles as $role) {
                    $result[] = $role->display_name;
                }

                return empty($result) ? 'N/A' : implode($result, ' | ');
            },
            'sortable' => false,
        ],
        'operation' => [
            'title'    => trans('administrator::administrator.operation'),
            'sortable' => false,
        ],
    ],

    'edit_fields' => [
        'name' => [
            'title' => trans('administrator::dashboard.permissions.name'),
        ],
        'display_name' => [
            'title' => trans('administrator::dashboard.permissions.display_name'),
        ],
        'description' => [
            'title' => trans('administrator::dashboard.permissions.description'),
        ],
    ],
    'filters' => [
        'name' => [
            'title' => trans('administrator::dashboard.permissions.name'),
        ],
        'display_name' => [
            'title' => trans('administrator::dashboard.permissions.display_name'),
        ],
        'description' => [
            'title' => trans('administrator::dashboard.permissions.description'),
        ],
    ],

    'actions' => [],
];