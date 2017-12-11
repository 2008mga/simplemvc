<?php namespace Simple\Kernel;

use Symfony\Component\Config\FileLocator;
use \Yosymfony\ConfigLoader\Config as ConfigLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;

class Config {

    private static $_instance;

    private $_locator;
    private $_repository;
    private $_loader;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function setLocator(array $configs)
    {
        $this->_locator = new FileLocator($configs);
        return $this;
    }

    public function setLoader()
    {
        $this->_loader = new ConfigLoader([
            new TomlLoader($this->_locator),
            new YamlLoader($this->_locator),
            new JsonLoader($this->_locator),
        ]);
        return $this;
    }

    public function load($repo)
    {
        $this->_repository = $this->_loader->load($repo);

        return $this->_repository;
    }
}