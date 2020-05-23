<?php

namespace classes;

/**
 * Class Configuration
 * @package classes
 */
class Configuration
{


    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }


    public function getDefaultController(): string
    {
        return getenv('DEFAULT_CONTROLLER');
    }

    public function getDefaultAction(): string
    {
        return getenv('DEFAULT_ACTION');
    }

    public function getDefaultRoute(): string
    {
        return $this->getDefaultController() . '/' . $this->getDefaultAction();
    }

    public function getTwigConfiguration(): array
    {
        return [
            'cache' => __DIR__ . '/../views/compilation_cache',
            'debug' => (int)getenv('TWIG_DEBUG'),
            'auto_reload' => (int)getenv('TWIG_AUTO_RELOAD'),
        ];
    }

    public function getConfigDb() :array
    {
        return [
            'name'=> getenv('MYSQL_DB_NAME'),
            'host'=> getenv('MYSQL_HOST'),
            'user'=>getenv('MYSQL_USER'),
            'pass'=> getenv('MYSQL_PASSWORD'),
            'port' => '3306',
        ];
    }

    public function getConfigTestDb() :array
    {
        return [
            'name'=> getenv('MYSQL_DB_NAME_TEST'),
            'host'=> getenv('MYSQL_HOST_TEST'),
            'user'=>getenv('MYSQL_USER_TEST'),
            'pass'=> getenv('MYSQL_PASSWORD_TEST'),
            'port' => getenv('MYSQL_PORT_TEST'),
        ];
    }

    public static function getInstance(): Configuration
    {
        static $configuration;
        if (null === $configuration) {
            $configuration = new self();
        }

        return $configuration;
    }
}