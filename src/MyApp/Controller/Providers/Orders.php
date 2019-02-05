<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Orders implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $orders = $app["controllers_factory"];
        $orders->get("/", "MyApp\\Controller\\OrdersController::ordersGet");// вывод списка заказов
        $orders->post("/", "MyApp\\Controller\\OrdersController::ordersPost");// создание заказа
        $orders
            ->get("/{id}", "MyApp\\Controller\\OrdersController::ordersIdGet")// вывод данных по заказу
            ->assert ('id', '\d+');
        $orders
            ->put("/{id}", "MyApp\\Controller\\OrdersController::ordersPut") // обновление данных по заказу
            ->assert ('id ', '\d+');
        $orders
            ->delete("/{id}", "MyApp\\Controller\\OrdersController::ordersDelete")// удаление заказа
            ->assert ('id ', '\d+ ');
//-
        $orders
            ->get("/books/{id}", "MyApp\\Controller\\OrdersController::ordersIdBooksGet") //книги принадлежащие заказу #id
            ->assert ('id ', '\d+ ');

        return $orders;
    }

}