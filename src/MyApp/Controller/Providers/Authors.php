<?php
namespace MyApp\Controller\Providers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Authors implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/", "MyApp\Controller\AuthsController::authorsGet");    // вывод списка авторов
        $authors->post("/", "MyApp\Controller\AuthsController::authorsPost");    // добавление нового автора
        $authors
            ->get("/{id}", "MyApp\Controller\AuthsController::authorsIdGet")    // вывод инф-ии об авторе
            ->assert ('id', '\d+');
        $authors
            ->put("/{id}", "MyApp\Controller\AuthsController::authorsIdPut")    // обновление данных автора
            ->assert ('id ', '\d+');
        $authors
            ->delete("/{id}", "MyApp\Controller\AuthsController::authorsIdDelete")    // удаление автора
            ->assert ('id ', '\d+ ');

        // доп-но
        $authors
            ->get("/{id}/books","MyApp\Controller\AuthsController::authorsIdBooksGet")//вывод списка книг, принадлежащих автору с #id
            ->assert ('id ', '\d+ ');//+
        $authors
            ->post("/{id}/books","MyApp\Controller\AuthsController::authorsIdBooksPost")//добавление новой книги автора с #id
            ->assert ('id ', '\d+ ');

        return $authors;
    }

}