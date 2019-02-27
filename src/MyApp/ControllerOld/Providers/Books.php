<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Books implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $books = $app["controllers_factory"];
        $books->get("/", "MyApp\\ControllerOld\\BooksController::booksGet");    // вывод списка книг
        $books
            ->get("/{id}", "MyApp\\ControllerOld\\BooksController::booksIdGet")    // вывод инф-ии о книге
            ->assert ('id', '\d+');

        $books->post("/", "MyApp\\ControllerOld\\BooksController::booksPost");    // добавление книги

//        $books->get("/{author}", "MyApp\\ControllerOld\\BooksController::authorIdbooksGet");    // вывод книг, написанных конкретным автором
        $books
            ->put("/{id}", "MyApp\Controller\BooksController::booksIdPut")// обновление данных о книге
            ->assert ('id ', '\d+');
        $books
            ->delete("/{id}", "MyApp\Controller\BooksController::booksIdDelete")    // удаление книги
            ->assert ('id ', '\d+');

        $books //перепроверить
            ->post("/{id}", "MyApp\Controller\BooksController::create")// добавление книги, написанной несколькими авторами
            ->assert ('id', '\d+');
        return $books;
    }
}
