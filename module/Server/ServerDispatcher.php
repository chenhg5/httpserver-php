<?php

namespace HttpServer\Module\Server;

use Exception;
use HttpServer\Module\Parser\Parser;

class ServerDispatcher
{

    /**
     * @param int $mode
     * @param Parser $parser
     * @return EpollModel|MultiFixedProcessModel|MultiProcessModel|MultiThreadModel|ReactorModel|SelectPollModel|SingleProcessCoroutineModel|SingleProcessModel
     * @throws Exception
     */
    public function select(int $mode, Parser $parser)
    {
        switch ($mode) {
            case 1:
                return new SingleProcessModel($parser);
            case 2:
                return new MultiFixedProcessModel($parser);
            case 3:
                return new MultiProcessModel($parser);
            case 4:
                return new MultiThreadModel($parser);
            case 5:
                return new SingleProcessCoroutineModel($parser);
            case 6:
                return new SelectPollModel($parser);
            case 7:
                return new EpollModel($parser);
            case 8:
                return new ReactorModel($parser);
            default:
                throw new Exception('require mode');

        }
    }
}