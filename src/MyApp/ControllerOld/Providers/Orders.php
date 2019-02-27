<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Orders implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $orders = $app["controllers_factory"];
        $orders->get("/", "MyApp\\ControllerOld\\OrdersController::ordersGet");// вывод списка заказов
        $orders->post("/", "MyApp\\ControllerOld\\OrdersController::ordersPost");// создание заказа
        $orders
            ->get("/{id}", "MyApp\\ControllerOld\\OrdersController::ordersIdGet")// вывод данных по заказу
            ->assert ('id', '\d+');
        $orders
            ->put("/{id}", "MyApp\\ControllerOld\\OrdersController::ordersPut") // обновление данных по заказу
            ->assert ('id ', '\d+');
        $orders
            ->delete("/{id}", "MyApp\\ControllerOld\\OrdersController::ordersDelete")// удаление заказа
            ->assert ('id ', '\d+ ');
//-
        $orders
            ->get("/books/{id}", "MyApp\\ControllerOld\\OrdersController::ordersIdBooksGet") //книги принадлежащие заказу #id
            ->assert ('id ', '\d+ ');

        return $orders;
    }

}