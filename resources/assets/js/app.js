/**
 * Created by qiuling.jiang on 2017/5/17.
 */
import Echo from "laravel-echo"
window.Vue = require('vue');
window.axios = require('axios');
window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'http://222.73.156.127:20063'
});