<?php
/**
 * Created by PhpStorm.
 * User: chenhg5
 * Date: 2018/4/21
 * Time: 上午11:35
 */

namespace HttpServer\Module\Server;


class SingleProcessCoroutineModel extends BaseModel
{
    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        //        // 单进程+协程模型
//        if ($mode == SINGLE_PROCESS_COROITINES_MODE) {
//            // TODO: 协程模型
//            // 参考: http://www.laruence.com/2015/05/28/3038.html
//        }
//
//        // 多进程folk模型：一个请求folk一个新进程，不阻塞请求
//        if ($mode == MULTI_PROCESS_MODE) {
//            while (1) {
//                // -1 永不超时
//                $conn = @stream_socket_accept($this->serv, -1);
//                // fork一个子进程
//                $pid = pcntl_fork();
//                if ($pid == 0) {
//                    echo "process id: " . posix_getpid() . "\n";
//
//                    $request = @fread($conn, 30000);  // 粗暴的设置长度
//                    echo "connected\n";
//                    echo "request info: \n\n" . $request . "\n";
//
//                    $response = <<<s
//HTTP/1.1 200 OK
//Server: Microsoft-IIS/4.0.1
//Date: Mon, 5 Jan 1993 13:13:33 GMT
//Content-Type: text/html
//Last-Modified: Mon, 5 Jan 2004 13:13:12 GMT
//Content-Length: 105
//
//<html><head><title>PHP HTTP SERVER RESPONSE</title></head><body>  Welcome to Php Http Server  </body></html>
//s;
//
//                    // ③ 返回数据
//                    @fwrite($conn, $response);
//
//                    fclose($conn);
//
//                    posix_kill(posix_getpid(), SIGTERM);
//
//                    exit(0);
//                }
//            }
//        }
//
    }
}