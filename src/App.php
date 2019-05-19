<?php


namespace src;

use src\Config\Routes;
use src\Services\Router;


/**
 * Point of entrance
 *
 * Class App
 * @package src
 */
class App
{
    /**
     * @var Router
     */
    private $router;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Start all the routes
     */
    public function run()
    {
        $routes = new Routes($this->router);
        $routes->start();
        $this->router->start();
    }
}