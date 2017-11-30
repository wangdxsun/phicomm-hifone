let https = require('https');
let fs = require('fs');
let options = {
    key:    fs.readFileSync('ssl/phicomm.com.key'),
    cert:   fs.readFileSync('ssl/phicomm.com.crt'),
    // ca:     fs.readFileSync('ssl/ca.crt')
};
let app = https.createServer(options);
let io = require('socket.io').listen(app);
let Redis = require('ioredis');
let redis = new Redis({
    host: '127.0.0.1',
    port: 6379,
    db: 1
});
redis.psubscribe('*');
io.on('connection', function(socket) {
    console.log('a new connection');
});
redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data);
    console.log(message);
});
app.listen(6001);