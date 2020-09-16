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
    protected $serv;

    protected $parser;

    protected $connectionsNum = 0;

    function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function run()
    {
        set_time_limit(0);

        // 使用 stream_* 而不用 socket_*
        // stream_* 支持更好
        // doc: https://stackoverflow.com/questions/9760548/php-sockets-vs-streams/9783856#9783856
        $this->serv = stream_socket_server("tcp://0.0.0.0:5005", $errno, $errstr) or die("create server failed");
    }
}


