<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Users implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $users = $app["controllers_factory"];
        $users->get("/", "MyApp\\Controller\\UsersController::usersGet");// вывод списка покупателей
        $users->post("/", "MyApp\\Controller\\UsersController::usersPost");// добавление покупателя
        $users
            ->get("/{id}", "MyApp\\Controller\\UsersController::usersIdGet")// вывод инф-ии о покупателе
            ->assert ('id', '\d+');
        $users
            ->put("/{id}", "MyApp\\Controller\\UsersController::usersIdPut")// обновление данных покупателя
            ->assert ('id ', '\d+');
        $users
            ->delete("/{id}", "MyApp\\Controller\\UsersController::usersIdDelete")// удаление покупателя
            ->assert ('id ', '\d+ ');

        $users
            ->get("/{id}/orders","MyApp\\Controller\\UsersController::usersIdOrdersGet")// вывод заказов покупателя id
            ->assert ('id ', '\d+ ');

        return $users;
    }

}