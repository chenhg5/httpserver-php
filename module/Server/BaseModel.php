<?php

namespace HttpServer\Module\Server;

use HttpServer\Module\Parser\Parser;

const SINGLE_PROCESS_MODE = 1;
const MULTI_PROCESS_MODE = 2;
const MULTI_PROCESS_FIXED_MODE = 3;
const MULTI_THREAD_MODE = 4;
const SELECT_POLL_MODE = 5;
const EPOLL_KQUEUE_MODE = 6;
const SINGLE_PROCESS_COROITINES_MODE = 7;

class BaseModel
{
    private $serv;

    protected $parser;

    function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function select($mode = null)
    {
        return $this;
    }

    public function run($mode = null)
    {
        set_time_limit(0);

        // 使用 stream_* 而不用 socket_*
        // stream_* 支持更好
        // doc: https://stackoverflow.com/questions/9760548/php-sockets-vs-streams/9783856#9783856
        $this->serv = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr) or die("create server failed");

        // 单进程模型：阻塞请求
        if (!$mode || $mode == SINGLE_PROCESS_MODE) {
            while (1) {
                // -1 永不超时
                $conn = @stream_socket_accept($this->serv, -1);
                echo "process id: " . posix_getpid() . "\n";

                // ① 获取并解析请求信息 HttpRequest
                $request = @fread($conn, 30000);  // 粗暴的设置长度
                echo "connected\n";
                echo "request info: \n\n" . $request . "\n";
                var_dump($this->parser->parseRequest($request));

                // ② 根据路由配置与请求判断响应类型：  $response = HttpProcessor::process($request)
                // 1. 直接返回静态页面数据, 读取文件[css, html, pdf, etc]         FileReader::read($request)
                // 2. http 反向代理/负载均衡, 转发请求, 获取响应, 返回客户端        ProxyHelper::forward($request)
                // 3. fastcgi[php/python/ruby/lua] -> 脚本解释器 -> 获取响应     Fastcgi::get($request)
                $response = <<<RES
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
RES;

                sleep(20);

                // 通用技术：
                // 1. 负载均衡
                // 2. 日志
                // 3. 缓存

                // ③ 返回数据
                @fwrite($conn, $response);

                fclose($conn);
            }
        }

        // 单进程+协程模型
        if ($mode == SINGLE_PROCESS_COROITINES_MODE) {
            // TODO: 协程模型
            // 参考: http://www.laruence.com/2015/05/28/3038.html
        }

        // 多进程folk模型：一个请求folk一个新进程，不阻塞请求
        if ($mode == MULTI_PROCESS_MODE) {
            while (1) {
                // -1 永不超时
                $conn = @stream_socket_accept($this->serv, -1);
                // fork一个子进程
                $pid = pcntl_fork();
                if ($pid == 0) {
                    echo "process id: " . posix_getpid() . "\n";

                    $request = @fread($conn, 30000);  // 粗暴的设置长度
                    echo "connected\n";
                    echo "request info: \n\n" . $request . "\n";

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

                    posix_kill(posix_getpid(), SIGTERM);

                    exit(0);
                }
            }
        }

        // 多进程固定进程抢占式模型 进程间通信
        if ($mode == MULTI_PROCESS_FIXED_MODE) {
            for ($i = 0; $i < 32; $i++) {
                if (pcntl_fork() == 0) {
                    while (1) {
                        $conn = stream_socket_accept($this->serv);
                        echo "process id: " . posix_getpid() . "\n";
                        $request = @fread($conn, 30000);  // 粗暴的设置长度
                        echo "connected\n";
                        echo "request info: \n\n" . $request . "\n";

                        $response = <<<s
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
s;

                        @fwrite($conn, $response);

                        fclose($conn);

                        fclose($conn);
                    }
                    exit(0);
                }
            }
        }

        // 多线程模型 线程锁 pthread扩展
        if ($mode == MULTI_THREAD_MODE) {
//            for ($i = 0; $i < 32; $i++) {
//                if ($thread = new Thread()) {
//                    while (1) {
//                        $conn = stream_socket_accept($this->serv);
//                        $request = fread($conn, 10);
//                        echo "connected\n";
//                        fclose($conn);
//                    }
//                    exit(0);
//                }
//            }
        }

        // select/poll 模型 同步非阻塞 IO
        if ($mode == SELECT_POLL_MODE) {


            function close($i, &$connections)
            {
                stream_socket_shutdown($connections[ $i ], STREAM_SHUT_RD);
                socket_close($connections[ $i ]);
                unset($connections[ $i ]);
            }

//            while (true)
//            {
//                $readfds = array_merge($connections, array($socket));
//                $writefds = array();
//
//                // 选择一个连接，获取读、写连接通道
//                if (stream_select($readfds, $writefds, $e = null, $t = 60))
//                {
//                    // 如果是当前服务端的监听连接
//                    if (in_array($socket, $readfds)) {
//                        // 接受客户端连接
//                        $newconn = stream_socket_accept($socket);
//                        $i = (int) $newconn;
//                        $reject = '';
//                        if (count($connections) >= 1024) {
//                            $reject = "Server full, Try again later./n";
//                        }
//                        // 将当前客户端连接放入 socket_select 选择
//                        $connections[$i] = $newconn;
//                        // 输入的连接资源缓存容器
//                        $writefds[$i] = $newconn;
//
//                        // 连接不正常
//                        if ($reject) {
//                            @fwrite($writefds[$i], $reject);
//                            unset($writefds[$i]);
//                            close($i, $connections);
//                        } else {
//                            echo "Client $i come./n";
//                        }
//                        // remove the listening socket from the clients-with-data array
//                        $key = array_search($socket, $readfds);
//                        unset($readfds[$key]);
//                    }
//
//                    // 轮循读通道
//                    foreach ($readfds as $rfd) {
//                        // 客户端连接
//                        $i = (int) $rfd;
//                        // 从通道读取
//                        $line = @socket_read($rfd, 2048, PHP_NORMAL_READ);
//                        if ($line === false) {
//                            // 读取不到内容，结束连接
//                            echo "Connection closed on socket $i./n";
//                            close($i, $connections);
//                            continue;
//                        }
//                        $tmp = substr($line, -1);
//                        if ($tmp != "/r" && $tmp != "/n") {
//                            // 等待更多数据
//                            continue;
//                        }
//                        // 处理逻辑
//                        $line = trim($line);
//                        if ($line == "quit") {
//                            echo "Client $i quit./n";
//                            close($i, $connections);
//                            break;
//                        }
//                        if ($line) {
//                            echo "Client $i >>" . $line . "/n";
//                        }
//                    }
//
//                    // 轮循写通道
//                    foreach ($writefds as $wfd) {
//                        $i = (int) $wfd;
//                        $w = socket_write($wfd, "Welcome Client $i!/n");
//                    }
//                }
//            }
        }

        // epoll/kqueue 模型 libevent 扩展 libev 事件库
        if ($mode == EPOLL_KQUEUE_MODE) {

        }


        // Reator模型
    }
}


