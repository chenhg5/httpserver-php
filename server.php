<?php

$serv = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr) or die("create server failed");

$i = 0;
while (1) {
    $conn = stream_socket_accept($serv);
    echo $i . "\n";
    $pid = pcntl_fork();
    if ($pid == 0) {
        echo "process id: " . posix_getpid() . "\n";
        $request = fread($conn, 10);
        echo "connected\n";
        fclose($conn);
        exit(0);
    }
    $i++;
}

// 多进程模型
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

// 多线程模型
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