<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Authors implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/", "MyApp\Controller\AuthsController::authorsGet");// show the list of authors
        $authors->post("/", "MyApp\Controller\AuthsController::authorsPost");// create a new author, using POST method
        $authors->get("/{id}", "MyApp\Controller\AuthsController::authorsIdGet")
            ->assert ('id', '\d+');// show the author #id
        $authors->put("/{id}", "MyApp\Controller\AuthsController::authorsIdPut")
            ->assert ('id ', '\d+');// update the author #id, using PUT method +
        $authors->delete("/{id}", "MyApp\Controller\AuthsController::authorsIdDelete")
            ->assert ('id ', '\d+ ');// delete the author #id, using DELETE method +

        // книги, принадлежащих конкретному автору
        $authors->get("/{id}/books","MyApp\Controller\AuthsController::authorsIdBooksGet")
            ->assert ('id ', '\d+ ');//+
        $authors->post("/{id}/books","MyApp\Controller\AuthsController::authorsIdBooksPost")
            ->assert ('id ', '\d+ ');//+
        return $authors;
    }

}