<?php

namespace HttpServer\Module\Server;

use HttpServer\Module\Parser\Parser;

class ServerDispatcher
{

    public function select($mode = null, Parser $parser)
    {
        return new SingleProcessModel($parser);
    }
}