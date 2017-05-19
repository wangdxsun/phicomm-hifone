var http = require('http').Server();
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis({
    host: '127.0.0.1',
    port: 6379,
    //password: 'feixun*123',
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
http.listen(6001);