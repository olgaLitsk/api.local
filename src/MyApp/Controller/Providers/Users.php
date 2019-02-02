<?php
namespace MyApp\Controller\Provider;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
class Users implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $users = $app["controllers_factory"];
//    $users->before(function(Application $app){
//        // check for something here
//    });
        $users->get("/list", "MyApp\Controller\UserController::index");// show the list of users +
        $users->post("/list", "MyApp\Controller\UserController::store");// create a new user, using POST method +
        $users->get("/list/{id}", "MyApp\Controller\UserController::show")->assert ('id', '\d+');// show the user #id +
        $users->put("/list/{id}", "MyApp\Controller\UserController::update")->assert ('id ', '\d+');// update the user #id, using PUT method +
        $users->delete("/list/{id}", "MyApp\Controller\UserController::destroy")->assert ('id ', '\d+ ');// delete the user #id, using DELETE method +

        //заказы клиента c #id
        $users->get("/{id}/orders","MyApp\Controller\UserController::getCustomerOrders")->assert ('id ', '\d+ ');  //list of orders customer #id

        return $users;
    }

}