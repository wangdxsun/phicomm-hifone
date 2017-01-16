<?php

return [
    'title'   => trans('administrator::dashboard.revisions.revisions'),
    'single'  => trans('administrator::dashboard.revisions.revisions'),
    'model'   => 'Hifone\Models\Revision',

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
    ],

    'columns' => [

        'id' => [
            'title' => 'ID',
        ],

        'revisionable_type' => [
            'title' => trans('administrator::dashboard.revisions.revisionable_type'),
        ],
        'revisionable_id' => [
            'title'    => trans('administrator::dashboard.revisions.revisionable_id'),
            'sortable' => false,
        ],

        'user' => [
            'title'    => trans('administrator::dashboard.revisions.author'),
            'sortable' => false,
            'output'   => function ($value, $model) {
                return admin_link(
                    $model->user->username,
                    'users',
                    $model->user_id
                );
            },
        ],
        'key' => [
            'title'    => trans('administrator::dashboard.revisions.key'),
        ],
        'log' => [
            'title'  => trans('administrator::dashboard.revisions.log'),
            'output' => function ($value, $model) {
                $html = "<div style='text-align:left;'>
                            <div style='text-indent:2em'>'old_value'&nbsp;&nbsp;&nbsp;=> '$model->old_value',</div>
                            <div style='text-indent:2em'>'new_value' => '$model->new_value'</div>
                        </div>";
                return $html;
            }
        ],
        'created_at' => [
            'title'    => trans('administrator::dashboard.revisions.created_at'),
        ],
        'operation' => [
            'title'  => trans('administrator::administrator.operation'),
            'output' => function ($value, $model) {

            },
            'sortable' => false,
        ],
    ],
    'edit_fields' => [
        'id' => [
            'title' => 'id'
        ]
    ],
    'filters' => [
        'revisionable_type' => [
            'title' => trans('administrator::dashboard.revisions.revisionable_type'),
        ],
        'revisionable_id' => [
            'title' => trans('administrator::dashboard.revisions.revisionable_id'),
        ],
        'user' => [
            'title'              => trans('administrator::dashboard.revisions.author'),
            'type'               => 'relationship',
            'name_field'         => 'username',
            'autocomplete'       => true,
            'search_fields'      => array("CONCAT(id, ' ', username)"),
            'options_sort_field' => 'id',
        ],
        'key' => [
            'title' => trans('administrator::dashboard.revisions.key'),
        ],
        'old_value' => [
            'title' => trans('administrator::dashboard.revisions.old_value'),
        ],
        'new_value' => [
            'title' => trans('administrator::dashboard.revisions.new_value'),
        ],
    ],
];