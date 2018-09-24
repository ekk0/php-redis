<?php
header("Content-type: text/html; charset=utf-8");
//创建websocket服务器对象，监听0.0.0.0:9502端口

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("192.168.1.120", 9500); 

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
	echo $data . $fd;
    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start(); 

?>