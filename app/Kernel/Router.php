<?php namespace Simple\Kernel;

use Simple\Kernel\Patterns\Singletons;
use \League\Route\RouteCollection;
use \League\Container\Container;
use \Zend\Diactoros\Response;
use \Zend\Diactoros\ServerRequestFactory;
use \Zend\Diactoros\Response\SapiEmitter;
use \League\Route\Http\Exception\NotFoundException;

class Router
{
    use Singletons;

    private static $_collection;
    private static $_routes;
    private static $_container;

    public function init()
    {
        if(is_null(self::$_collection)) {
            $this->__containerBuild();
            self::$_collection = new RouteCollection(self::$_container);
        }

        return $this;
    }

    private function __containerBuild()
    {
        self::$_container = new Container;
        self::$_container->share('response', Response::class);
        self::$_container->share('request', function () {
            return ServerRequestFactory::fromGlobals(
                $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
            );
        });

        self::$_container->share('emitter', SapiEmitter::class);
    }

    public function dispatch()
    {
        try {
            $container = self::$_container;
            $response = self::$_collection->dispatch($container->get('request'), $container->get('response'));
            $container->get('emitter')->emit($response);
        } catch (NotFoundException $e) {
            exit(404);
        }
    }

    public function collection()
    {
        return self::$_collection;
    }
}