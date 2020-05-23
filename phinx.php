<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env');
}

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => getenv("MYSQL_HOST"),
            'name' => getenv("MYSQL_DB_NAME"),
            'user' => getenv("MYSQL_USER"),
            'pass' => getenv("MYSQL_PASSWORD"),
            'port' => getenv("MYSQL_PORT"),
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => getenv("MYSQL_HOST_TEST"),
            'name' => getenv("MYSQL_DB_NAME_TEST"),
            'user' => getenv("MYSQL_USER_TEST"),
            'pass' => getenv("MYSQL_PASSWORD_TEST"),
            'port' => getenv("MYSQL_PORT_TEST"),
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];