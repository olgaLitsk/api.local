<?php
namespace MyApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class BooksController
{
    // show the list of books
    public function index(Application $app)
    {
        $sql = "SELECT * FROM books";
//        $sql = "SELECT book_id, title, shortdescription, firstname, lastname, name FROM books as b
//                LEFT JOIN authors as a ON b.category = a.author_id
//                LEFT JOIN categories as c ON c.category_id = b.category";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'The book was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    // show the book #id
    public function show(Application $app, $id)
    {//нужно ли join-ить таблицы, в качестве шв использовать число или slug
        $sql = "SELECT * FROM books WHERE book_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The book was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post,200);
    }

    // create a new book, using POST method
    /* public function create(Application $app,Request $request){//нет смылса создавать книгу без указания автора?
         $parametersAsArray = [];
         if ($content = $request->getContent()) {
             $parametersAsArray = json_decode($content, true);
         }
         $constraint = new Assert\Collection(array(
             'title' => new Assert\Type('string'),
             'shortdescription'  => new Assert\Type('string'),
             'price' => new Assert\Type('double'),
             'author' => new Assert\Type('integer'),
             'category' => new Assert\Type('integer'),
         ));

         $errors = $app['validator']->validate($parametersAsArray, $constraint);

         $errs_msg = [];
         if (count($errors) > 0) {
             foreach ($errors as $error) {
                 $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
             }
             return new Response(json_encode($errs_msg),404);
         }else{
             $app['db']->insert('books', $parametersAsArray);
             $lastInsertId = $app['db']->lastInsertId();
             $app['db']->insert('authors_books',array('book' => $lastInsertId, 'author' => $parametersAsArray['author']));
             return $app->redirect('/books/list/' . $lastInsertId, 201);
         }
     }*/

    // update the book #id, using PUT method
    public function update(Application $app,Request $request, $id)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = [];
        if (isset($parametersAsArray['title'])){
            $constraintArr['title'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['shortdescription'])){
            $constraintArr['shortdescription'] = new Assert\Type('string');
        }
        if (isset($parametersAsArray['price'])){
            $constraintArr['price'] = new Assert\Type('double');
        }
        if (isset($parametersAsArray['author'])){
            $constraintArr['author'] = new Assert\Type('integer');
        }
        if (isset($parametersAsArray['category'])){
            $constraintArr['category'] = new Assert\Type('integer');
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
            $app['db']->update('books', $parametersAsArray, array('book_id' => $id));
        }
        return $app->json('book updated',200);
    }

    // delete the book #id, using DELETE method
    public function destroy(Application $app, $id)
    {
        try {
            $sql = "SELECT * FROM books WHERE book_id = ?";
            $bookInfo = $app['db']->fetchAssoc($sql, array($id));
            if (!$bookInfo)
                return new Response('The book not found', 404);
            $app['db']->delete('books', array(
                    'book_id' => $bookInfo['book_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
        return new Response('Custormer Deleted', 200);
    }
}
