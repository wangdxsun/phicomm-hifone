{{--安卓端版块图片--}}
<div class="form-group row">
    <div class="col-xs-4">
        <label>{{ '安卓端首页热门版块图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageUrlAndroidHandle">
            <img v-if="imageUrlAndroid" :src="imageUrlAndroid" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input v-model="imageUrlAndroid" type="hidden" name="node[android_icon]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ '安卓端版块列表图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageListUrlAndroidHandle">
            <img v-if="imageListUrlAndroid" :src="imageListUrlAndroid" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageListUrlAndroid" placeholder="请输入内容" type="hidden" name="node[android_icon_list]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ '安卓端版块详情页图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageDetailUrlAndroidHandle">
            <img v-if="imageDetailUrlAndroid" :src="imageDetailUrlAndroid" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageDetailUrlAndroid" placeholder="请输入内容" type="hidden" name="node[android_icon_detail]"></el-input>
    </div>
</div>
{{--IOS端版块图片--}}
<div class="form-group row">
    <div class="col-xs-4">
        <label>{{ 'IOS端首页热门版块图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageUrlIosHandle">
            <img v-if="imageUrlIos" :src="imageUrlIos" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input v-model="imageUrlIos" type="hidden" name="node[ios_icon]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ 'IOS端版块列表图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageListUrlIosHandle">
            <img v-if="imageListUrlIos" :src="imageListUrlIos" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageListUrlIos" placeholder="请输入内容" type="hidden" name="node[ios_icon_list]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ 'IOS端版块详情页图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageDetailUrlIosHandle">
            <img v-if="imageDetailUrlIos" :src="imageDetailUrlIos" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageDetailUrlIos" type="hidden" name="node[ios_icon_detail]"></el-input>
    </div>
</div>
{{--H5端版块图片--}}
<div class="form-group row">
    <div class="col-xs-4">
        <label>{{ 'H5端首页热门版块图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageUrlHandle">
            <img v-if="imageUrl" :src="imageUrl" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input v-model="imageUrl" type="hidden" name="node[icon]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ 'H5端版块列表图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageListUrlHandle">
            <img v-if="imageListUrl" :src="imageListUrl" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageListUrl" type="hidden" name="node[icon_list]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ 'H5端版块详情页图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageDetailUrlHandle">
            <img v-if="imageDetailUrl" :src="imageDetailUrl" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            <el-input  v-model="imageDetailUrl" type="hidden" name="node[icon_detail]"></el-input>
        </el-upload>
    </div>
</div>
{{--WEB端版块图片--}}
<div class="form-group row">
    <div class="col-xs-4">
        <label>{{ 'WEB端版块详情页图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageDetailUrlWebHandle">
            <img v-if="imageDetailUrlWeb" :src="imageDetailUrlWeb" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input v-model="imageDetailUrlWeb" type="hidden" name="node[web_icon_detail]"></el-input>
    </div>

    <div class="col-xs-4">
        <label>{{ 'WEB端右侧列表页图片' }}</label><br>
        <el-upload
                class="avatar-uploader"
                action="/upload_image"
                :show-file-list="false"
                :on-success="imageListUrlWebHandle">
            <img v-if="imageListUrlWeb" :src="imageListUrlWeb" class="el-avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-input  v-model="imageListUrlWeb" type="hidden" name="node[web_icon_list]"></el-input>
    </div>
</div>