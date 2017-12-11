<?php namespace Simple\Kernel;

use Simple\Kernel\Patterns\Singletons;
use Illuminate\Database\Capsule\Manager as Capsule;

class App {
    use Singletons;

    protected static $singletons = [];
    private static $_init = false;

    public function init()
    {
        if (!self::$_init) {
            $this->singleton('config', function () {
                return Config::getInstance()
                    ->setLocator(self::$_instance->getConfigDirs())
                    ->setLoader();
            });

            $this->singleton('database', function () {
                $capsule = new Capsule;

                $capsule->addConnection(config('database.json!config'));

                $capsule->bootEloquent();
            });

            $this->singleton('router', function () {
                return Router::getInstance()
                    ->init();
            });

            self::$_init = true;
        }

        return $this;
    }

    public function singleton($name, $data)
    {
        if (!isset(self::$singletons[$name])) {
            self::$singletons[$name] = $data;
        }

        return $this;
    }

    public function make($name, $params = null)
    {
        if (in_array($name, array_keys(self::$singletons))) {
            $singleton = self::$singletons[$name];

            return (is_callable($singleton)) ? call_user_func($singleton, $params) : $singleton;
        }

        return false;
    }

    public function getConfigDirs()
    {
        return [
            realpath(__DIR__ ) . '/../../config'
        ];
    }
}