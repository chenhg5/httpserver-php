<?php

namespace HttpServer\Module\Server;

use HttpServer\Module\Thread\HttpThread;

class MultiThreadModel extends BaseModel
{
    public function run()
    {
        parent::run();

        // 多线程模型 线程锁 pthread扩展
        $threadPool = [];
        for ($i = 0; $i < 4; $i++) {
            $threadPool[$i] = new HttpThread($i);
            $threadPool[$i]->start();
        }

        // zend_mm_heap corrupted 问题:
        // 修改 output_buffering=4096 为 disable
        // export USE_ZEND_ALLOC=0
        while (true) {
            $nowConn = @stream_socket_accept($this->serv, -1);
            if ($nowConn == false) {
                unset($nowConn);
                continue;
            }
            for ($i = 0; $i < count($threadPool); $i++) {
                if (is_null($threadPool[$i]->conn)) {
                    echo "dispatch job $i. \n";
                    $threadPool[$i]->conn = $nowConn;
                    break;
                }
            }
        }
    }
}