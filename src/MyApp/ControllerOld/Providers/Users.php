<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Users implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $users = $app["controllers_factory"];
        $users->get("/", "MyApp\\ControllerOld\\UsersController::usersGet");// вывод списка покупателей
        $users->post("/", "MyApp\\ControllerOld\\UsersController::usersPost");// добавление покупателя
        $users
            ->get("/{id}", "MyApp\\ControllerOld\\UsersController::usersIdGet")// вывод инф-ии о покупателе
            ->assert ('id', '\d+');
        $users
            ->put("/{id}", "MyApp\\ControllerOld\\UsersController::usersIdPut")// обновление данных покупателя
            ->assert ('id ', '\d+');
        $users
            ->delete("/{id}", "MyApp\\ControllerOld\\UsersController::usersIdDelete")// удаление покупателя
            ->assert ('id ', '\d+ ');

        $users
            ->get("/{id}/orders","MyApp\\ControllerOld\\UsersController::usersIdOrdersGet")// вывод заказов покупателя id
            ->assert ('id ', '\d+ ');

        return $users;
    }

}