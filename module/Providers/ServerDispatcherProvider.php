<?php

namespace HttpServer\Module\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use HttpServer\Module\Server\ServerDispatcher;

class ServerDispatcherProvider implements ServiceProviderInterface {

    public function register(Container $pimple)
    {
        $pimple['dispatcher'] = function () {
            return new ServerDispatcher();
        };
    }
}