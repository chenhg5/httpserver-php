<?php

namespace HttpServer\Module\Thread;

use Thread;

class HttpThread extends Thread
{
    public $conn;

    public $index;

    public $mode;

    public $runing = false;

    public function __construct($index, $mode = 1, $conn = null)
    {
        $this->index = $index;
        $this->conn = $conn;
        $this->mode = $mode;
        $this->runing = true;
    }

    public function run()
    {
        echo self::getCurrentThreadId() . '_' . $this->index . ' is ready.' . "\n";
        if ($this->mode == 1) {
            // while 循环导致cpu爆炸 需要usleep  可考虑协程帮助调度
            while ($this->runing) {
                if (!$this->conn) {
                    usleep(10000); // 微观层面释放cpu资源 不阻塞占用
                    continue;
                }

                $request = @fread($this->conn, 30000);
                var_dump($request);
                echo self::getCurrentThreadId() . '_' . $this->index . " is working\n";
                echo "request info: \n\n" . $request . "\n";

                $response = <<<RES
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
RES;
//                sleep(30);
                @fwrite($this->conn, $response);
                fclose($this->conn);
                $this->conn = null;
            }
        } else {
            $request = @fread($this->conn, 30000);
            var_dump($request);
            echo self::getCurrentThreadId() . '_' . $this->index . " is working\n";
            echo "request info: \n\n" . $request . "\n";

            $response = <<<RES
HTTP/1.1 200 OK
Server: Microsoft-IIS/4.0.1
Date: Mon, 5 Jan 1993 13:13:33 GMT
Content-Type: text/html
Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
Content-Length: 105

<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
RES;
//            sleep(30);
            @fwrite($this->conn, $response);
            fclose($this->conn);
            $this->conn = null;
        }
    }
}