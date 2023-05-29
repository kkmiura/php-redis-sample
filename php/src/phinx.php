<?php

return
[
    'paths' => [
        'migrations' => './db/migrations',
        'seeds' => './db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'mysql',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => 'mysql',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'mysql',
            'name' => 'development_db',
            'user' => 'root',
            'pass' => 'mysql',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'mysql',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => 'mysql',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
