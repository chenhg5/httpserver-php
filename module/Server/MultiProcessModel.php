<?php

namespace HttpServer\Module\Server;

use function posix_kill;
use function pcntl_signal;

class MultiProcessModel extends BaseModel
{

    public $childList = [];

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        declare(ticks = 1);

        if (!function_exists('pcntl_signal'))
        {
            echo "Error, you need to enable the pcntl extension in your php binary, 
                  see http://www.php.net/manual/en/pcntl.installation.php for more info.";
            exit(1);
        }

        pcntl_signal(SIGINT, function () {
            for ($i = 0; $i < count($this->childList); $i++) {
                posix_kill($this->childList[ $i ], SIGINT);
            }
            die();
        });

        echo "\nmaster id is " . posix_getpid() . ", press ctrl+c to stop \n";

        // 多进程固定进程抢占式模型 进程间通信
        while (1) {
            $conn = @stream_socket_accept($this->serv, -1);
            if ($conn == false) continue;
            $pid = pcntl_fork();
            if ($pid == 0) {
                echo "process id: " . posix_getpid() . "\n";

                $request = "";
                // 粗暴的正则
                while (!preg_match('/\r?\n\r?\n/', $request)) {
                    $request .= fread($conn, 1024);
                }
                echo "connected\n";
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

                @fwrite($conn, $response);

                fclose($conn);

                unset($conn);
            } elseif ($pid > 0) {
                if (!in_array($pid, $this->childList)) {
                    $this->childList[] = $pid;
                }
            } else {
                echo 'fork fail!';
            }
        }
    }
}