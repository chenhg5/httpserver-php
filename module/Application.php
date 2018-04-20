<?php
/**
 * Created by PhpStorm.
 * User: chenhg5
 * Date: 2018/4/20
 * Time: 下午10:19
 */

namespace HttpServer\Module;

use Pimple\Container;

/**
 * Class Application
 * @package HttpServer\Module
 *
 * @property \HttpServer\Module\Server\ServerModel $server
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

//        $this->registerConfig($config)
//            ->registerProviders()
//            ->registerLogger()
//            ->registerRequest()
//            ->registerHttpClient();

        $this->registerProviders();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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