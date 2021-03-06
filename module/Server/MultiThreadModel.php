<?php

namespace HttpServer\Module\Server;

use function count;
use HttpServer\Module\Thread\HttpThread;
use function is_null;

class MultiThreadModel extends BaseModel
{
    public function run()
    {
        parent::run();

        $i = 0;
        $threadPool = [];

        // zend_mm_heap corrupted 问题:
        // 修改 output_buffering=4096 为 disable
        // export USE_ZEND_ALLOC=0
        while (true) {
            $nowConn = @stream_socket_accept($this->serv, -1);
            if ($nowConn == false) {
                continue;
            }

            $length = count($threadPool);
            for ($j = 0; $j < $length; $j++) {
                if (isset($threadPool[$j]) && isset($threadPool[$j]->conn) && is_null($threadPool[$j]->conn)) {
//                    $threadPool[$j]->kill();
                    unset($threadPool[$j]);
                }
            }

            $threadPool[$i] = new HttpThread($i, 2, $nowConn);
            $threadPool[$i]->start();
            $i++;
        }
    }
}