<?php
namespace MyApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AuthsController
{
    // show the list of authors
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

    // show the author #id
    public function authorsIdGet(Application $app, $id)
    {
        $sql = "SELECT * FROM authors WHERE author_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The author was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post,200);
    }

    // create a new author, using POST method
    public function authorsPost(Application $app,Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $constraint = new Assert\Collection(array(
            'firstname' => new Assert\Type('string'),
            'lastname'  => new Assert\Type('string'),
            'about' => new Assert\Type('string'),
        ));

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg,404);
        }else{
            $app['db']->insert('authors', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            return $app->redirect('/authors/list/' . $lastInsertId, 201);
        }
    }

    // update the author #id, using PUT method
    public function authorsIdPut(Application $app,Request $request, $id)
    {//получился PATCH метод
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = [];
        if (isset($parametersAsArray['firstname'])){
            $constraintArr['firstname'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['lastname'])){
            $constraintArr['lastname'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['about'])){
            $constraintArr['about'] = new Assert\Type('string');
        }

        $constraint = new Assert\Collection($constraintArr);

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg,404);
        }else{
            $app['db']->update('authors', $parametersAsArray, array('author_id' => $id));
        }

        return $app->json('Author updated',200);

    }

    // delete the author #id, using DELETE method
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

    //вывод списка книг, принадлежащих автору с #id
    public function authorsIdBooksGet(Application $app, $id)
    {
        //list of books author #id
        $sql = "SELECT * FROM books as b
                LEFT JOIN authors_books as ab ON b.book_id = ab.book
                LEFT JOIN authors as a ON a.author_id = ab.author
                WHERE author=?";
        $post = $app['db']->fetchAll($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The books were not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    //добавление новой книги автора с #id
    public function authorsIdBooksPost(Application $app, Request $request, $id)
    {
        $sql1 = "SELECT * FROM books WHERE title=?";
        $sql2 = "SELECT * FROM authors_books WHERE author =?";

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        //делаем проверку на существование книги в таблице books
        $checkBooks = $app['db']->fetchAssoc($sql1, array((string) $parametersAsArray['title']));
        //если записи нет
        if (!$checkBooks['book_id']){
            //вставляем запись о новой книге в таблицу books
            $app['db']->insert('books', $parametersAsArray);
            //извлекаем id последней вставленной записи
            $lastInsertId = $app['db']->lastInsertId();
            //вставляем запись book_id, author_id в промежуточную таблицу authors_books
            $app['db']->insert('authors_books', array('author' =>$id, 'book' => $lastInsertId));
        }else{
            //делаем проверку на существование author_id в таблице authors_books
            $checkAuthors = $app['db']->fetchAssoc($sql2, array((string) $id));
            if ($checkAuthors['author']){
                return $app->json('The book of author has added', 200);
            }
            //вставляем запись book_id, author_id в промежуточную таблицу authors_books
            $app['db']->insert('authors_books', array('author' =>$id, 'book' => $checkBooks['book_id']));
        }
        return new Response('The book added',200);
    }



}
