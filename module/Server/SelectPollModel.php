<?php

namespace HttpServer\Module\Server;

class SelectPollModel extends BaseModel
{
    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        // select/poll 模型 同步非阻塞 IO
        $master = [];
        $master[] = $this->serv;
        $_w = [];
        $_e = null;
        while (1) {
            $read = $master;
            // 解占用cpu
            usleep(10000);
            // 这里的io复用指的是通过select的系统调用不采用accept的阻塞io，从而让io释放出来可以
            // accept其他的连接，io准备就绪后再通知用户这边
            // 注意select可以选择阻塞和不阻塞 不阻塞的话其实也是防止读取不完整等，
            // 参考链接：https://www.zhihu.com/question/37271342
            if (stream_select($read, $_w, $_e, 600)) {
                foreach ($read as $rstream) {
                    // 如果可读的select里面有服务端socket，就是有新的连接进来
                    // 要处理新的连接
                    if ($this->serv == $rstream) {
                        $conn = stream_socket_accept($rstream);
                        if ($conn == false) {
                            continue;
                        }
                        // 加入此连接，后续进行确认
                        if (!in_array($conn, $master)) {
                            $master[] = $conn;
                        }
                    } else {
                        // 否则即为旧的连接
                        $conn = $rstream;
                    }

                    if ($conn !== false) {
                        // 粗暴的正则
                        $sock_data = "";
                        while (!preg_match('/\r?\n\r?\n/', $sock_data)) {
                            $sock_data .= fread($conn, 1024);
                        }
                        // 确认连接已经读完了，长连接需要，短连接一般就关闭了
                        if (!$sock_data) {
                            unset($master[array_search($conn, $master)]);
                            fclose($conn);
                            continue;
                        }
                        $fid = (int)$conn;
                        echo "connected! here is connection $fid\n";
//                        echo "request info: \n\n" . $sock_data . "\n";

                        $response = <<<RES
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
RES;
                        @fwrite($conn, $response);
                    }
                }
            }
        }
    }
}