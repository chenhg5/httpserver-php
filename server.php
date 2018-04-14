<?php

set_time_limit(0);

// 使用 stream_* 而不用 socket_*
// stream_* 支持更好
// doc: https://stackoverflow.com/questions/9760548/php-sockets-vs-streams/9783856#9783856

$serv = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr) or die("create server failed");

// stream_set_blocking($serv , false);

// 单进程模型
$i = 0;
while (1) {
    // 永不超时
    $conn = @stream_socket_accept($serv, -1);
    echo $i . "\n";
    $pid = pcntl_fork();
    if ($pid == 0) {
        echo "process id: " . posix_getpid() . "\n";

        // 获取请求信息
        $request = @fread($conn, 10);
        echo "connected\n";
        echo "request info: " . $request . "\n";

        // 根据请求信息与路由配置指向指定路由 获取信息构造返回
        $response = <<<s
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 2004 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
s;

        @fwrite($conn, $response);

        fclose($conn);
        exit(0);
    }
    $i++;
}


// 多进程模型 进程间通信
//for ($i = 0; $i < 32; $i++) {
//    if (pcntl_fork() == 0) {
//        while (1) {
//            $conn = stream_socket_accept($serv);
//            echo "process id: " . posix_getpid() . "\n";
//            $request = fread($conn, 10);
//            echo "connected\n";
//            fclose($conn);
//        }
//        exit(0);
//    }
//}

// 多线程模型 线程锁
//for ($i = 0; $i < 32; $i++) {
//    if ($thread = new Thread()) {
//        while (1) {
//            $conn = stream_socket_accept($serv);
//            $request = fread($conn, 10);
//            echo "connected\n";
//            fclose($conn);
//        }
//        exit(0);
//    }
//}

// select poll epoll/kqueue 模型 libev 事件库

// Reator模型