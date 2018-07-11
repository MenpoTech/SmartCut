var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8890);

io.on('connection', function (socket) {

    console.log("new client connected");

    var new_tocr = redis.createClient();
    var rearranged = redis.createClient();

    new_tocr.subscribe('new_tocr');
    rearranged.subscribe('chat_triggered');

    new_tocr.on("message", function(channel, message) {
        console.log("New message: " + message + ". In channel: " + channel);
        socket.emit(channel, message);
    });

    rearranged.on("message", function(channel, message) {
        console.log("New message: " + message + ". In channel: " + channel);
        socket.emit(channel, message);
    });

    socket.on('disconnect', function() {
        rearranged.quit();
        new_tocr.quit();
    });

});