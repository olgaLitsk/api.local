<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Books implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $books = $app["controllers_factory"];

        $books->get("/list", "MyApp\Controller\BooksController::index");// show the list of books +

        $books->post("/list", "MyApp\Controller\BooksController::create");// create a new book, using POST method +

        $books->get("/{id}", "MyApp\Controller\BooksController::show")->assert ('id', '\d+');// show the book #id +

        $books->put("/{id}", "MyApp\Controller\BooksController::update")->assert ('id ', '\d+');// update the book #id, using PUT method +

        $books->delete("/{id}", "MyApp\Controller\BooksController::destroy")->assert ('id ', '\d+ ');// delete the book #id, using DELETE method +

        return $books;
    }

}