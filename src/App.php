<?php


namespace src;

use src\Config\Routes;
use src\Router\Router;

require_once __DIR__ . '/Services/Router.php';
require_once __DIR__ . '/Config/Routes.php';


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