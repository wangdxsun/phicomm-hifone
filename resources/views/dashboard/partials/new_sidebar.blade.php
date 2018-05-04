<div id="app">
    <el-row class="tac">
        <el-col :span="4">
            <el-menu
                    default-active="2"
                    class="el-menu-vertical-demo"
                    @open="handleOpen"
                    @close="handleClose"
                    router="true"
            >
            <el-menu-item index="system">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">系统概况</span>
                </template>
            </el-menu-item>

            <el-submenu index="thread">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span>帖子管理</span>
                </template>
                    <el-menu-item index="thread-thread">发帖管理</el-menu-item>
                    <el-menu-item index="thread-reply">回帖管理</el-menu-item>
                    <el-submenu index="thread-node">
                        <template slot="title">板块管理</template>
                        <el-menu-item index="thread-node-section">分类管理</el-menu-item>
                        <el-menu-item index="thread-node-node">主版块管理</el-menu-item>
                        <el-menu-item index="thread-node-subNode">子版块管理</el-menu-item>
                    </el-submenu>
                    <el-menu-item index="thread-banner">banner管理</el-menu-item>
            </el-submenu>

            <el-submenu index="user">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">用户管理</span>
                </template>
                    <el-menu-item index="user-user">用户管理</el-menu-item>
                    <el-menu-item index="user-group">用户组管理</el-menu-item>
                    <el-menu-item index="user-credit">经验值管理</el-menu-item>
                    <el-menu-item index="user-tag">标签管理</el-menu-item>
            </el-submenu>

            <el-submenu index="question">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">问答管理</span>
                </template>
                    <el-menu-item index="question-question">提问管理</el-menu-item>
                    <el-menu-item index="question-answer">回答管理</el-menu-item>
                    <el-menu-item index="question-comment">回复管理</el-menu-item>
                    <el-menu-item index="question-tag">问题分类管理</el-menu-item>
            </el-submenu>

            <el-menu-item index="word">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">敏感词过滤</span>
                </template>
            </el-menu-item>

            <el-menu-item index="chat">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">群发消息</span>
                </template>
            </el-menu-item>

            <el-menu-item index="report">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">举报管理</span>
                </template>
            </el-menu-item>

            <el-menu-item index="stat">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">数据统计</span>
                </template>
            </el-menu-item>

            <el-menu-item index="log">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">操作日志</span>
                </template>
            </el-menu-item>

            <el-menu-item index="setting">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">系统设置</span>
                </template>
            </el-menu-item>

            <el-menu-item index="index" route="{{ url('/') }}">
                <template slot="title">
                    <i class="el-icon-location"></i>
                    <span slot="title">返回首页</span>
                </template>
            </el-menu-item>

            </el-menu>
        </el-col>
    </el-row>
</div>



<script>
    new Vue({
        el: '#app',
        methods: {
            handleOpen(key, keyPath) {
                console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                console.log(key, keyPath);
            }
        }
    });
</script>