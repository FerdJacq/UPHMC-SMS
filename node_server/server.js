
var express = require("express")
var bodyParser = require("body-parser")
var app = express()
var redis = require('redis')
var fs = require('fs')

var server_type = "default";

if (server_type=="server_live")
{
  var options = {
    //SERVER 
    key: fs.readFileSync("/etc/letsencrypt/live/renewal.psmed.org/privkey.pem"),
    cert: fs.readFileSync("/etc/letsencrypt/live/renewal.psmed.org/fullchain.pem")
  }
}
else if (server_type=="server_local")
{
  var options = {
    //SERVER 
    key: fs.readFileSync("/etc/ssl/private/ssl-cert-snakeoil.key"),
    cert: fs.readFileSync("/etc/ssl/certs/ssl-cert-snakeoil.pem")
  }
}
else if (server_type=="xampp")
{
  var options = {
    //SERVER 
    key: fs.readFileSync("C:/xampp/apache/conf/ssl.key/server.key"),
    cert: fs.readFileSync("C:/xampp/apache/conf/ssl.crt/server.crt")
  }
}
else
{
  var options = {
    // Localhost
     key: fs.readFileSync("ssl/ssl.key/server.key"),
     cert: fs.readFileSync("ssl/ssl.crt/server.crt")
  }
}

var http = require('http')
var https = require('https')
// https_server = https.Server(options,app)
http_server = http.Server(app)
var io_server = http_server
var io = require('socket.io')(io_server, {pingInterval: 500})


//============================== REDIS ==============================//
let redisClient = redis.createClient();

redisClient.subscribe('new_transaction');
redisClient.on('error', function(err){ 
  console.error('Redis error:', "connection timeout"); 
});

redisClient.on("connect", function () {
    console.log('redis connected');
});

redisClient.on("message", function(channel, message) 
{
    console.log(message);
    var message_data = JSON.parse(message);
    io.sockets.emit(channel, message_data);
});
//============================== REDIS ==============================//


//============================== SOCKET ==============================//
io_server.listen(8891);

io.sockets.on('connection', (socket) => {
  // console.log("=====================")
  var address = socket.request.connection.remoteAddress;
  console.log('New connection from ' + address.replace("::", "").replace("ffff:", ""));

});


io.on('connection', function (socket) 
{
    socket.on('channel_connect', function (id) {
        let channel = id;
        socket.join(channel);
        console.log("Created a token channel", channel);
    });
})