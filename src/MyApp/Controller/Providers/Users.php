<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Users implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $users = $app["controllers_factory"];
//    $users->before(function(Application $app){
//        // check for something here
//    });
        $users->get("/", "MyApp\Controller\UsersController::index");// show the list of users +
        $users->post("/", "MyApp\Controller\UsersController::store");// create a new user, using POST method +
        $users->get("/{id}", "MyApp\Controller\UsersController::show")->assert ('id', '\d+');// show the user #id +
        $users->put("/{id}", "MyApp\Controller\UsersController::update")->assert ('id ', '\d+');// update the user #id, using PUT method +
        $users->delete("/{id}", "MyApp\Controller\UsersController::destroy")->assert ('id ', '\d+ ');// delete the user #id, using DELETE method +

        //заказы клиента c #id
        $users->get("/{id}/orders","MyApp\Controller\UsersController::getCustomerOrders")->assert ('id ', '\d+ ');  //list of orders customer #id

        return $users;
    }

}