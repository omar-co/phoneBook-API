<?php


namespace src;

use src\Config\Routes;
use src\Services\Router;

//TODO: Add PHPDoc Blocks
class App
{
    private $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run()
    {
        $routes = new Routes($this->router);
        $routes->start();
        $this->router->start();
    }
}