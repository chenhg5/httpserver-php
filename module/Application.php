<?php

namespace HttpServer\Module;

//use Monolog\Handler\ErrorLogHandler;
//use Monolog\Handler\HandlerInterface;
//use Monolog\Handler\StreamHandler;
use Pimple\Container;
use Monolog\Logger;

/**
 * Class Application
 * @package HttpServer\Module
 *
 * @property \HttpServer\Module\Server\BaseModel $server
 */
class Application extends Container
{

    /**
     * @var array
     */
    protected $providers = [
        Providers\ServerProvider::class
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * Constructor.
     *
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        parent::__construct($prepends);

        $this->registerConfig($config)
            ->registerLogger()
            ->registerRequest()
            ->registerResponse()
            ->registerProviders();
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }


    /**
     * Register service providers.
     *
     * @return $this
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }

        return $this;
    }

    /**
     * Register application config.
     *
     * @param array $config
     * @return $this
     */
    protected function registerConfig(array $config)
    {
        $this['config'] = $config;
        return $this;
    }

    /**
     * Register logger.
     *
     * @return $this
     */
    protected function registerLogger()
    {
        if (isset($this['logger'])) {
            return $this;
        }

        $logger = new Logger(str_replace('\\', '.', strtolower(get_class($this))));

//        if ($logFile = $this['config']['log.file']) {
//            $logger->pushHandler(new StreamHandler(
//                    $logFile,
//                    $this['config']->get('log.level', Logger::WARNING),
//                    true,
//                    $this['config']->get('log.permission', null))
//            );
//        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
//            $logger->pushHandler($this['config']['log.handler']);
//        } else {
//            $logger->pushHandler(new ErrorLogHandler());
//        }

        $this['logger'] = $logger;

        return $this;
    }

    /**
     * Register server request handler.
     *
     * @return $this
     */
    protected function registerRequest()
    {
        $this['parser'] = function () {
            return new Parser\Parser();
        };
        return $this;
    }

    /**
     * Register server response handler.
     *
     * @return $this
     */
    protected function registerResponse()
    {
        return $this;
    }

    /**
     * @param null $mode
     */
    public function run($mode = null)
    {
        $this->server->select($mode)->run();
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }
}