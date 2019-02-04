<?php

namespace MyApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AuthsController
{
    public function authorsGet(Application $app)
    {
        $sql = "SELECT * FROM authors";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'Authors are absent');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function authorsIdGet(Application $app, $id)
    {
        $sql = "SELECT * FROM authors WHERE author_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int)$id));
        if (!$post) {
            $error = array('message' => 'The author was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function authorsPost(Application $app, Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraint = new Assert\Collection(array(
            'firstname' => new Assert\Type('string'),
            'lastname' => new Assert\Type('string'),
            'about' => new Assert\Type('string'),
        ));
        $errors = $app['validator']->validate($parametersAsArray, $constraint);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg, 404);
        } else {
            $app['db']->insert('authors', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            return $app->redirect('/authors/' . $lastInsertId, 201);
        }
    }

    public function authorsIdPut(Application $app, Request $request, $id)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = [];
        if (isset($parametersAsArray['firstname'])) {
            $constraintArr['firstname'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['lastname'])) {
            $constraintArr['lastname'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['about'])) {
            $constraintArr['about'] = new Assert\Type('string');
        }
        $constraint = new Assert\Collection($constraintArr);
        $errors = $app['validator']->validate($parametersAsArray, $constraint);
        $errs_msg = [];

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg, 404);
        } else {
            $app['db']->update('authors', $parametersAsArray, array('author_id' => $id));
        }
        return $app->json('Author updated', 200);
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
