<?php

namespace HttpServer\Module\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use HttpServer\Module\Server\ServerModel;

class ServerProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['server'] = function () {
            return new ServerModel();
        };
    }
}