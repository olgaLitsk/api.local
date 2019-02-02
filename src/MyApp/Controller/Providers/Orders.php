<?php
namespace MyApp\Controller\Provider;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
class Orders implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $orders = $app["controllers_factory"];
        $orders->get("/list", "MyApp\Controller\OrdersController::index");// show the list of Orders +
        $orders->post("/list", "MyApp\Controller\OrdersController::create");// create a new Order, using POST method +
        $orders->get("/{id}", "MyApp\Controller\OrdersController::show")->assert ('id', '\d+');// show the Order #id +
        $orders->put("/{id}", "MyApp\Controller\OrdersController::update")->assert ('id ', '\d+');// update the Order #id, using PUT method +
        $orders->delete("/{id}", "MyApp\Controller\OrdersController::destroy")->assert ('id ', '\d+ ');// delete the Order #id, using DELETE method +

        //книги принадлежащие заказу #id
        $orders->get("/books/{id}", "MyApp\Controller\OrdersController::ordersBooksIdGet");//-

        return $orders;
    }

}