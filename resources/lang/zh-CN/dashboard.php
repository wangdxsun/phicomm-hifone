<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'dashboard' => '控制台',
    'overview'  => '概况',

    'attentions' => [
        'attentions' => 'Attentions',
        'add'        => '添加公告',
    ],

    'content' => [
        'content' => '内容管理',
    ],
    'pages' => [
        'pages'   => '页面',
        'slug'    => '路径',
        'title'   => '标题',
        'body'    => '内容',
        'add'     => [
            'title'   => '添加页面',
            'success' => '页面添加成功.',
        ],
        'edit'     => [
            'title'   => '编辑页面',
            'success' => '页面更新成功.',
        ],
    ],
    'photos' => [
        'photos' => '图片',
    ],
    'threads'  => [
        'threads' => '话题',
        'add'     => [
            'title'   => '添加话题',
            'success' => '话题添加成功.',
        ],
        'edit' => [
            'title'   => '编辑话题',
            'success' => '话题更新成功.',
        ],
        'batch' => [
            'audit'   => '批量审核通过',
        ],
    ],
    'replies' => [
        'replies' => '回帖',
        'edit'    => [
            'title' => '编辑回贴',
        ],
        'batch' => [
            'audit'   => '批量审核通过',
        ],
    ],

    'sections' => [
        'sections'     => '所属分类',
        'name'         => '名称',
        'order'        => '排序',
        'add'          => [
            'title'   => '添加分类',
            'message' => '暂无分类',
            'success' => '分类添加成功。',
            'failure' => '分类添加失败！',
        ],
        'edit' => [
            'title'   => '编辑分类',
            'success' => '分类信息更新成功。',
            'failure' => '分类更新失败！',
        ],
    ],
    'notices' => [
        'notice'        => '公告管理',
        'title'        => '标题',
        'content'      => '内容',
        'start_time'   => '起始时间',
        'end_time'     => '终止时间',
        'notice_type'=> [
            'title'   => '公告类型',
            'type_1'  => '文字公告',
            'type_2'  => '网址链接'
        ],
        'add'         => [
            'title'   => '添加公告',
            'success' => '添加公告成功.',
            'failure' => '添加公告失败',
        ],
        'edit'         => [
            'title'   => '编辑公告',
            'success' => '编辑公告成功.',
            'failure' => '编辑公告失败',
        ],
    ],
    'words' => [
        'word'        => '敏感词过滤',
        'content'     => '敏感词汇',
        'replacement'  => '替换为',
        'type'=> [
            'title'   => '词语分类',
            'type_0'  => '默认',
            'type_1'  => '政治',
            'type_2'  => '广告',
            'type_3'  => '涉枪涉爆',
            'type_4'  => '网络招嫖',
            'type_5'  => '淫秽信息'
        ],
        'action'=> [
            'title'   => '过滤状态',
            'type_1'  => '审核敏感词',
            'type_2'  => '禁止敏感词',
            'type_3'  => '替换敏感词'
        ],
        'add'         => [
            'head_title'   => '添加',
            'title'   => '添加词语',
            'batch_in'=> '批量导入',
            'success' => '添加成功.',
            'failure' => '添加失败',
        ],
        'edit'         => [
            'head_title'   => '添加',
            'title'   => '编辑词语',
            'batch_out'=> '全部导出',
            'batch_del'=> '批量删除',
            'success' => '编辑成功.',
            'failure' => '编辑失败',
        ],
    ],
    'nodes' => [
        'nodes'        => '主板块管理',
        'sub_nodes'    => '子板块管理',
        'name'         => '主板块名称',
        'sub_name'     => '子板块名称',
        'parent'       => '所属板块',
        'root'         => '根板块',
        'status_name'  => '状态',
        'description'  => '板块描述',
        'icon'         => [
            'hot'      => '首页热门板块图片',
            'list'     => '板块列表图片',
            'detail'   => '板块详情页图片',
        ],
        'slug'         => 'Slug',
        'slug_help'    => '快捷路径',
        'add'          => [
            'title'    => '添加主板块',
            'sub_title'=> '添加子板块',
            'success'  => '板块添加成功。',
            'failure'  => '板块添加失败！',
        ],
        'moderator' => [
            'add'      =>  '添加版主',
            'type'     =>  '所属版主类别',
            'list'     =>  '版主列表',
        ],
        'edit' => [
            'title'     => '编辑主板块',
            'sub_title' => '编辑子板块',
            'success'   => '板块信息更新成功。',
            'failure'   => '板块更新失败！',
        ],

        'status'       => [
            0 => '正常',
            1 => '隐藏',
            2 => '会员可见',
        ],
        // Node parents
        'parents' => [
            'parents'        => '版块|板块',
            'no_nodes'       => '没有版块，马上添加一个吧',
            'add'            => [
                'title'   => '添加版块',
                'success' => 'Node group added.',
                'failure' => 'Something went wrong with the node group, please try again.',
            ],
            'edit' => [
                'title'   => '编辑版块',
                'success' => 'Node group updated.',
                'failure' => 'Something went wrong with the node group, please try again.',
            ],
            'delete' => [
                'success' => '版块已删除。',
                'failure' => 'The node group could not be deleted, please try again.',
            ],
        ],
    ],

    'adblocks' => [
        'adblocks' => '广告位类型',
        'name'     => '名称',
        'slug'     => '标识',
        'add'      => [
            'title'   => '添加广告位类型',
            'success' => '广告位类型添加成功.',
        ],
        'edit' => [
            'success' => '广告位类型信息更新成功.',
        ],
    ],
    'adspaces' => [
        'adspaces' => '广告位',
        'name'     => '名称',
        'position' => '位置标识',
        'route'    => '投放范围',
        'add'      => [
            'title'   => '添加广告位',
            'success' => '广告位添加成功.',
        ],
        'edit' => [
            'success' => '广告位信息更新成功.',
        ],
    ],

    'advertisements' => [
        'advertisements' => '广告管理',
        'name'           => '广告名称',
        'body'           => '广告内容',
        'add'            => [
            'title'   => '添加广告',
            'success' => '广告添加成功.',
        ],
        'edit' => [
            'success' => '广告信息更新成功.',
        ],
    ],

    'tips' => [
        'tips'        => '小贴士',
        'body'        => '内容',
        'status'      => '是否显示',
        'add'         => [
            'title'   => '添加小贴士',
            'success' => '小提示添加成功.',
            'message' => '当前没有小贴士.',
        ],
        'edit' => [
            'title'   => '编辑小贴士',
            'success' => '小贴士更新成功.',
        ],
        'delete' => [
            'success' => '小贴士已删除。',
            'failure' => 'The tip could not be deleted, please try again.',
        ],
    ],

    'locations' => [
        'locations'        => '热门城市',
        'name'             => '城市名',
        'add'              => [
            'title'   => '添加热门城市',
            'success' => '热门城市添加成功.',
            'message' => '当前没有热门城市.',
        ],
        'edit' => [
            'title'   => '编辑热门城市',
            'success' => '热门城市更新成功.',
        ],
        'delete' => [
            'success' => '热门城市已删除。',
            'failure' => 'The location could not be deleted, please try again.',
        ],
    ],

    'users' => [
        'users'       => '用户管理',
        'user'        => ':email, 注册于 :date',
        'username'    => '用户名',
        'email'       => '邮箱地址',
        'password'    => '密码',
        'description' => '用户列表',
        'add'         => [
            'title'   => '注册用户',
            'success' => '用户注册成功.',
            'failure' => '用户注册失败',
        ],
        'edit'     => [
            'title'   => '编辑用户',
            'success' => '用户更新成功.',
        ],
    ],

    'links' => [
        'links'       => '友情链接',
        'title'       => '网站名称',
        'url'         => '网址',
        'cover'       => 'LOGO地址',
        'description' => '描述',
        'status'      => '是否显示',
        'add'         => [
            'title'   => '添加友情链接',
            'success' => '友情链接添加成功.',
            'message' => '当前没有友情链接.',
        ],
        'edit' => [
            'title'   => '编辑友情链接',
            'success' => '友情链接修改成功.',
        ],
        'delete' => [
            'success' => '友情链接已删除。',
            'failure' => 'The link could not be deleted, please try again.',
        ],
    ],

    // Settings
    'settings' => [
        'settings'    => '系统设置',
        'general'     => [
            'general'     => '网站设置',
            'images-only' => 'Only images may be uploaded.',
            'too-big'     => '您上传的文件太大了。上传的图像大小应小于:size',
            'site_name'   => '网站名称',
            'site_domain' => '网址',
            'site_logo'   => '网站logo',
            'site_cdn'    => 'CDN地址',
            'site_about'  => '关于我们',
            'logo'        => 'Logo设置',
            'logo_help'   => '推荐使用90*40大小的logo.',
            'auto_audit'  => '自动审帖',
        ],
        'localization' => [
            'localization' => '系统语言',
        ],
        'customization' => [
            'customization' => '首页路由',
            'controller'    => 'Controller',
            'method'        => 'Method',
        ],
        'stylesheet' => [
            'stylesheet' => '自定义样式',
            'custom_css' => '自定义样式表',
        ],
        'theme' => [
            'theme'                   => '界面设置',
            'background-color'        => 'Background Color',
            'background-fills'        => '区块填充色(组件, 故障, 页尾)',
            'banner-background-color' => '横幅背景色',
            'banner-padding'          => '横幅Padding值',
            'fullwidth-banner'        => '启用全宽横幅？',
            'text-color'              => '文字颜色',
            'dashboard-login'         => '在页尾显示 管理后台 的入口？',
            'reds'                    => '红 (用于错误类提示)',
            'blues'                   => '蓝 (用于信息类提示)',
            'greens'                  => '绿 (用于成功类提示)',
            'yellows'                 => '黄 (用于警告类提示)',
            'oranges'                 => '橙 (用于通知类提示)',
            'metrics'                 => '图表填充色',
            'links'                   => '链接',
            'per_page'                => '分页',
        ],
        'aboutus' => [
            'aboutus'    => '关于我们',
            'version'    => 'Hifone版本',
            'php'        => '服务器系统及 PHP',
            'webserver'  => 'Web服务器',
            'db'         => '数据库',
            'cache'      => '缓存驱动',
            'session'    => 'Session驱动',
            'team'       => '开发团队',
        ],
        'edit' => [
            'success' => '设置已更新.',
            'failure' => 'Settings could not be saved.',
        ],
    ],

    // Sidebar footer
    'help'        => '帮助',
    'home'        => '首页',
    'logout'      => '退出',

];
