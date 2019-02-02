<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Books implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $books = $app["controllers_factory"];
        $books->get("/", "MyApp\Controller\BooksController::booksGet");    // вывод списка книг
        $books->post("/", "MyApp\Controller\BooksController::booksPost");    // добавление книги
        $books
            ->get("/{id}", "MyApp\Controller\BooksController::booksIdGet")    // вывод инф-ии о книге
            ->assert ('id', '\d+');
        $books
            ->put("/{id}", "MyApp\Controller\BooksController::booksIdPut")// обновление данных о книге
            ->assert ('id ', '\d+');
        $books
            ->delete("/{id}", "MyApp\Controller\BooksController::booksIdDelete")    // удаление книги
            ->assert ('id ', '\d+ ');
        return $books;
    }
}
