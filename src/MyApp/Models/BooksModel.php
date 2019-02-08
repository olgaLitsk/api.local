<?php

namespace MyApp\Models;

use Silex\Application;

class BooksModel
{
    public function booksGet(Application $app, $data)
    {
        $sql = "SELECT * FROM books";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'The book was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }
}
