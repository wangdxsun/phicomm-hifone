<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- 引入样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
</head>
<body>
<div id="app">
    <el-upload
            class="avatar-uploader"
            action="/upload_image"
            :show-file-list="false"
            :on-success="handleAvatarSuccess1">
        <img v-if="imageUrl1" :src="imageUrl1" class="avatar">
        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
    <el-input type="hidden" v-model="imageUrl1" placeholder="请输入内容"></el-input>
    <el-upload
            class="avatar-uploader"
            action="/upload_image"
            :show-file-list="false"
            :on-success="handleAvatarSuccess2">
        <img v-if="imageUrl2" :src="imageUrl2" class="avatar">
        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
    <el-input v-model="imageUrl2" placeholder="请输入内容"></el-input>
</div>
</body>
<!-- 先引入 Vue -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                imageUrl1: '',
                imageUrl2: ''
            };
        },
        methods: {
            handleAvatarSuccess1: function (res) {
                this.imageUrl1 = res.filename;
            },
            handleAvatarSuccess2: function (res) {
                this.imageUrl2 = res.filename;
            }
        }
    })
</script>
<style>
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #20a0ff;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
</style>
</html>