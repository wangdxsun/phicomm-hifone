<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
</head>
<body>
<div id="app">
    <img src='http://hifone1.dm.dev.phiwifi.com:1885/uploads/images/2017/10/24/LcBnYqELtN.jpg' class='message_image' v-preview='http://hifone1.dm.dev.phiwifi.com:1885/uploads/images/2017/10/24/LcBnYqELtN.jpg'/>
</div>
</body>
<!-- 先引入 Vue -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

<script>
    new Vue({
        el: '#app',
        data: function() {
            return {
            }
        }
    })
</script>
</html>