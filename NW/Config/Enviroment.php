<?php
declare(strict_types=1);

namespace NW\Config;

class Enviroment
{
    // Add your own variables here, these examples are all fake
    public static array $config = [
        'game_server_host' => '127.0.0.1',
        'game_server_port' => '2593',
        'db_dsn' => 'mysql:host=localhost;dbname=nw;charset=utf8mb4'
    ];
}