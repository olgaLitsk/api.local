<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Authors implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/list", "MyApp\Controller\AuthorsController::authorsGet");// show the list of authors +
        $authors->post("/list", "MyApp\Controller\AuthorsController::authorsPost");// create a new author, using POST method +
        $authors->get("/{id}", "MyApp\Controller\AuthorsController::authorsIdGet")->assert ('id', '\d+');// show the author #id +
        $authors->put("/{id}", "MyApp\Controller\AuthorsController::authorsIdPut")->assert ('id ', '\d+');// update the author #id, using PUT method +
        $authors->delete("/{id}", "MyApp\Controller\AuthorsController::authorsIdDelete")->assert ('id ', '\d+ ');// delete the author #id, using DELETE method +

        // книги, принадлежащих конкретному автору
        $authors->get("/{id}/books","MyApp\Controller\AuthorsController::authorsBooksIdGet")->assert ('id ', '\d+ ');//+
        $authors->post("/{id}/books","MyApp\Controller\AuthorsController::authorsBooksIdPost")->assert ('id ', '\d+ ');//+
        return $authors;
    }

}