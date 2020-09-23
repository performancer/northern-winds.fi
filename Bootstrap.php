<?php
declare(strict_types=1);

use NW\Router;

class Bootstrap
{
    public function __construct()
    {
        define('ROOT_PATH', realpath(__DIR__));
        define('PUBLIC_PATH', ROOT_PATH . '/public');
        define('APP_PATH', ROOT_PATH . '/NW');
        define('VIEW_PATH', APP_PATH . '/View');

        spl_autoload_register(static function($className) {
            $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            require dirname(APP_PATH) . '/' . $className . '.php';
        });
    }

    public function run(): void
    {
        session_start();
        (new Router())->route();
    }
}