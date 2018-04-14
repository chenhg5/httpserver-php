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
    // -1 永不超时
    $conn = @stream_socket_accept($serv, -1);
    echo $i . "\n";
    // fork一个进程
    $pid = pcntl_fork();
    if ($pid == 0) {
        echo "process id: " . posix_getpid() . "\n";

        // ① 获取并解析请求信息 HttpRequest
        $request = @fread($conn, 30000);  // 粗暴的设置长度
        echo "connected\n";
        echo "request info: \n\n" . $request . "\n";

        // ② 根据路由配置与请求判断响应类型：  $response = HttpProcessor::process($request)
        // 1. 直接返回静态页面数据, 读取文件[css, html, pdf, etc]         FileReader::read($request)
        // 2. http 反向代理/负载均衡, 转发请求, 获取响应, 返回客户端        ProxyHelper::forward($request)
        // 3. fastcgi[php/python/ruby/lua] -> 脚本解释器 -> 获取响应     Fastcgi::get($request)
        $response = <<<s
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
s;

        // ③ 返回数据
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