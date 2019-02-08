<?php

namespace MyApp\Models;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorsModel
{
    public function authorsGet(Application $app)
    {
        $sql = "SELECT * FROM authors";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'Authors are absent');
            return $error;
        }
        return $post;
    }

    public function authorsIdGet(Application $app, $id)
    {
        $sql = "SELECT * FROM authors WHERE author_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int)$id));
        if (!$post) {
            $error = array('message' => 'The author was not found.');
            return $error;
        }
        return $post;
    }

    public function authorsPost(Application $app, $data)
    {
        $app['db']->insert('authors', $data);

        $lastInsertId = $app['db']->lastInsertId();
        return $lastInsertId;
    }

    public function authorsIdPut(Application $app, $data)
    {
        $id = $data->getId();
        $app['db']->update('authors', $data, array('author_id' => $id));
     }

    public function authorsIdDelete(Application $app, $id)
    {
        try {
            $sql = "SELECT * FROM authors WHERE author_id = ?";
            $authorInfo = $app['db']->fetchAssoc($sql, array($id));

            if (!$authorInfo)
                return new Response('author not found', 404);

            $app['db']->delete('authors', array(
                    'author_id' => $authorInfo['author_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
        return new Response('The author Deleted', 200);
    }

    public function authorsIdBooksGet(Application $app, $id)
    {
        //list of books author #id - фича
        $sql = "SELECT * FROM books as b
                LEFT JOIN authors_books as ab ON b.book_id = ab.book
                LEFT JOIN authors as a ON a.author_id = ab.author
                WHERE author=?";
        $post = $app['db']->fetchAll($sql, array((int)$id));
        if (!$post) {
            $error = array('message' => 'The books were not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }
}
