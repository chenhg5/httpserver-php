<?php

namespace HttpServer\Module\Server;

use function count;
use function sleep;

class SingleProcessCoroutineModel extends BaseModel
{

    protected $connectionsNum = 4;

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        // 单进程+协程模型 提高并发 然鹅不能并行 没卵用
        stream_set_blocking($this->serv, 0);
        $i = 0;
        $handleClientCoroutineArr = [];

        while (true) {
            $clientSocket = stream_socket_accept($this->serv, -1);
            if ($clientSocket == false) {
                continue;
            }
            $i++;
            echo "request $i come in \n";
            $handleClientCoroutineArr[] = $this->handleClient($clientSocket);
            if ($i == $this->connectionsNum) {
                for ($j = 0; $j < count($handleClientCoroutineArr); $j++) {
                    $handleClientCoroutineArr[$j]->send('');
                }
            }
        }
    }

    public function handleClient($conn)
    {
        yield $request = @fread($conn, 30000);  // 粗暴的设置长度
        echo "connected\n";
        echo "request info: \n\n" . $request . "\n";
        // var_dump($this->parser->parseRequest($request));

        // ② 根据路由配置与请求判断响应类型：$response = HttpProcessor::process($request)
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

        // 通用技术：
        // 1. 负载均衡
        // 2. 日志
        // 3. 缓存

        sleep(5);

        // ③ 返回数据
        @fwrite($conn, $response);

        fclose($conn);
        unset($conn);
    }
}