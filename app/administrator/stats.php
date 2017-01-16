<?php

use Illuminate\Support\Facades\Auth;

return [
    'title'   => trans('administrator::dashboard.stats.stats'),
    'single'  => trans('administrator::dashboard.stats.stats'),
    'model'   => 'Hifone\Models\Stats',

    'permission' => function () {
        // return Auth::user()->hasRole('Developer');
        return true;
    },

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

    'columns' => [
        'id' => [
            'title' => 'ID',
        ],
        'day' => [
            'title'    => trans('administrator::dashboard.stats.day'),
            'sortable' => false,
        ],
        'register_count' => [
            'title'    => trans('administrator::dashboard.stats.register_count'),
        ],
        'thread_count' => [
            'title'    => trans('administrator::dashboard.stats.thread_count'),
        ],
        'reply_count' => [
            'title'    => trans('administrator::dashboard.stats.reply_count'),
        ],
        'image_count' => [
            'title'    => trans('administrator::dashboard.stats.image_count'),
        ],
        'operation' => [
            'title'    => trans('administrator::administrator.operation'),
            'sortable' => false,
        ],
    ],

    'edit_fields' => [
        'day' => [
            'title' => trans('administrator::dashboard.stats.day'),
        ],
    ],
    'filters' => [
        'day' => [
            'title' => trans('administrator::dashboard.stats.day'),
        ],
    ],
];