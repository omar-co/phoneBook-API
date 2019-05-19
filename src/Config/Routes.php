<?php


namespace src\Config;

use Db\PhoneBook;
use src\Manager\PhoneBookManager;
use src\Services\Router;


/**
 * Class Routes
 * @package src\Config
 */
class Routes
{
    /**
     * @var Router
     */
    private $router;

    /**
     * Routes constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Here are defined all the routes for the API
     *
     * I use GET|POST|PUT|DELETE HTTP verbs
     */
    public function start()
    {
        $router = $this->router;

        $router->set404(function () {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo '404, route not found!';
        });

        $router->mount('/contact', function () use ($router) {

            $router->get('/', function () {
                 PhoneBookManager::all();
            });

            $router->post('/', function () {
                $data = json_decode(file_get_contents("php://input"), true);
                PhoneBookManager::save($data);
            });

            $router->get('/(\d+)', function ($id) {
                PhoneBookManager::get($id);
            });

            // UPDATES
            $router->mount('/update', function () use ($router) {

                $router->put('/user/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::UpdateUser($id, $data);
                });

                $router->put('/email/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::UpdateEmail($id, $data);
                });

                $router->put('/phone/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::UpdatePhone($id, $data);
                });
            });

            //DELETES
            $router->mount('/delete', function () use ($router) {

                $router->delete('/user/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::DeleteUser($id, $data);
                });

                $router->delete('/email/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::DeleteEmail($id, $data);
                });

                $router->delete('/phone/(\d+)', function ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    PhoneBookManager::DeletePhone($id, $data);
                });
            });
        });


  }
}