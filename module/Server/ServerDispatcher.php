<?php

namespace HttpServer\Module\Server;

use HttpServer\Module\Parser\Parser;

class ServerDispatcher
{

    public function select($mode = null, Parser $parser)
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
            default:
                return new SingleProcessModel($parser);

        }
    }
}