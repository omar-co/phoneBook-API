<?php


namespace src\Config;

use Db\PhoneBook;
use src\Manager\PhoneBookManager;
use src\Services\Router;

//TODO: AddPHPDOC Blocks
class Routes
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function start()
    {
        $router = $this->router;

        $router->set404(function () {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo '404, route not found!';
        });

        $router->mount('/user', function () use ($router) {

            $router->get('/', function () {
                $all = PhoneBookManager::all();
               echo json_encode($all);
            });

            $router->post('/', function () {
                echo 'add user';
            });

            $router->get('/(\d+)', function ($id) {
                echo 'user data id ' . htmlentities($id);
            });

            $router->put('/(\d+)', function ($id) {
                echo 'Update user id ' . htmlentities($id);
            });
        });
  }
}