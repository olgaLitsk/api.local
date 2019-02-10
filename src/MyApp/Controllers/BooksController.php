<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\Book;
use MyApp\Models\ORM\Category;
use MyApp\Models\ORM\Author;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class BooksController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $books = $app["controllers_factory"];
        $books->get("/", "MyApp\\Controllers\\BooksController::showAction");    // вывод списка книг
        $books
            ->get("/{id}", "MyApp\\Controllers\\BooksController::showActionId")// вывод инф-ии о книге
            ->assert('id', '\d+');

        $books->post("/", "MyApp\\Controllers\\BooksController::createAction");    // добавление книги

//        $books->get("/{book}", "MyApp\\Controllers\\BooksController::bookIdbooksGet");    // вывод книг, написанных конкретным автором
        $books
            ->put("/{id}", "MyApp\\Controllers\\BooksController::updateAction")// обновление данных о книге
            ->assert('id ', '\d+');
        $books
            ->delete("/{id}", "MyApp\\Controllers\\BooksController::deleteAction")// удаление книги
            ->assert('id ', '\d+');

        $books//перепроверить
        ->post("/{id}", "MyApp\\Controllers\\BooksController::create")// добавление книги, написанной несколькими авторами
        ->assert('id', '\d+');
        return $books;
    }

    public function showAction(Application $app)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Book');
            $query = $repository->createQueryBuilder('a')->getQuery();
            $books = $query->getArrayResult();
            return $app->json($books, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function showActionId(Application $app, $id)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Book');
            $query = $repository->createQueryBuilder('a')
                ->where('a.book_id = :identifier')
                ->setParameter('identifier', $id)
                ->getQuery();
            $books = $query->getArrayResult();

            if (!$books) {
                $error = array('message' => 'Not found book for id ' . $id);
                return $app->json($error, 404);
            }

            return $app->json($books, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function createAction(Application $app, Request $request)
    {
        try {
            $content = json_decode($request->getContent(), true);
            $category = new Category();
            $category->setName($content['category']);

            $book = new Book();
            $book->setTitle($content['title']);
            $book->setShortdescription($content['shortdescription']);
            $book->setPrice($content['price']);
            $book->setCategory($category);
//var_dump($book);return;
            $errors = $app['validator']->validate($book);

            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);

            } else {
                $app['em']->persist($category);

                $app['em']->persist($book);
                $app['em']->flush();

                $book_id = $book->getBookId();
                return $app->redirect('/books/' . $book_id, 201);
            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        try {
        $content = json_decode($request->getContent(), true);

        $book = $app['em']->getRepository('MyApp\Models\ORM\Book')
            ->find($id);
        $bookCategory = $book->getCategory()->getCategoryId();

        $category = $app['em']->getRepository('MyApp\Models\ORM\Category')
            ->find($bookCategory);

        $category->setName($content['category']);
        $book->setTitle($content['title']);
        $book->setShortdescription($content['shortdescription']);
        $book->setPrice($content['price']);

        $errors = $app['validator']->validate($book);//не работает валидация

        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg, 404);

        } else {
            $app['em']->flush();

            $book_id = $book->getBookId();
            return $app->redirect('/books/' . $book_id, 201);
        }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function deleteAction(Application $app, $id)
    {
        try {
            $book = $app['em']->getRepository('MyApp\Models\ORM\Book')
                ->find($id);
            if (!$book) {
                $error = array('message' => 'Not found book id ' . $id);
                return $app->json($error, 404);
            }

            $bookCategory = $book->getCategory()->getCategoryId();
            $category = $app['em']->getRepository('MyApp\Models\ORM\Category')
                ->find($bookCategory);

            $app['em']->remove($category);
            $app['em']->remove($book);
            $app['em']->flush();
            return $app->json(array('message' => 'The book id ' . $id . ' deleted'), 200);
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
    }

    // добавление книги, написанной несколькими авторами
    public function create(Application $app, Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraint = new Assert\Collection(array(
            'title' => new Assert\Type('string'),
            'shortdescription' => new Assert\Type('string'),
            'price' => new Assert\Type('double'),
            'book' => new Assert\NotBlank(),
            'category' => new Assert\Type('integer'),
        ));

        $errors = $app['validator']->validate($parametersAsArray, $constraint);
        $books = $parametersAsArray["book"];
        unset($parametersAsArray["book"]);

        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return new Response(json_encode($errs_msg), 404);
        } else {
            $app['db']->insert('books', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            foreach ($books as $val) {
                $app['db']->insert('books_books', array('book' => $lastInsertId, 'book' => $val));
            }
            return $app->redirect('/books/' . $lastInsertId, 201);
        }
    }

}