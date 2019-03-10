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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BooksController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $books = $app["controllers_factory"];
        $books->get("/", "MyApp\\Controllers\\BooksController::getBooks");    // вывод списка книг
        $books
            ->get("/{id}", "MyApp\\Controllers\\BooksController::getBook")// вывод инф-ии о книге
            ->assert('id', '\d+');
        $books
            ->post("/", "MyApp\\Controllers\\BooksController::postBook")// добавление книги
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            });
        $books
            ->put("/{id}", "MyApp\\Controllers\\BooksController::putBook")// обновление данных о книге
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+');
        $books
            ->delete("/{id}", "MyApp\\Controllers\\BooksController::deleteBook")// удаление книги
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+');
        return $books;
    }

    public function getBooks(Application $app, Request $request)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Book');
            $query = $repository->createQueryBuilder('a');

            if ($request->get('search')) {
                $query
                    ->where(
                        $query->expr()->like('a.title', ':identifier')
                    )
                    ->setParameter('identifier', '%'.$request->get('search').'%');
            }
            $books = $query->getQuery()->getArrayResult();
            return $app->json($books, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function getBook(Application $app, $id)
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

    public function postBook(Application $app, Request $request)
    {
        try {
            $content = json_decode($request->getContent(), true);
            $category = $app['em']->getRepository('MyApp\Models\ORM\Category')
                ->find($content['category']);
            if (!$category) {
                return $app->json(array('message' => 'Not found category id ' . $content['category']));
            }
            $book = new Book();
            $book->setTitle($content['title']);
            $book->setShortdescription($content['shortdescription']);
            $book->setPrice($content['price']);
            $book->setCategory($category);
            $authors = array();
            foreach ($content['authors'] as $k) {
                if (!$app['em']->getRepository('MyApp\Models\ORM\Author')->find($k)) {
                    return $app->json(array('message' => 'Not found author id ' . $k));
                }
                $authors[$k] = $app['em']->getRepository('MyApp\Models\ORM\Author')->find($k);
            }
            $book->setAuthor($authors);
//            foreach ($content['authors'] as $key) {
//                if (!$app['em']->getRepository('MyApp\Models\ORM\Author')->find($key)) {
//                    return $app->json(array('message' => 'Not found author id ' . $key));
//                }
//                $book->addAuthor($app['em']->getRepository('MyApp\Models\ORM\Author')->find($key));
//            }
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

    public function putBook(Application $app, Request $request, $id)
    {
        try {
            $content = json_decode($request->getContent(), true);
            $category = $app['em']->getRepository('MyApp\Models\ORM\Category')
                ->find($content['category']);

            if (!$category) {
                return $app->json(array('message' => 'Not found category id ' . $content['category']));
            }
            $book = $app['em']->getRepository('MyApp\Models\ORM\Book')
                ->find($id);
            $book->setTitle($content['title']);
            $book->setShortdescription($content['shortdescription']);
            $book->setPrice($content['price']);
            $book->setCategory($category);

            $authors = array();
            foreach ($content['authors'] as $k) {
                if (!$app['em']->getRepository('MyApp\Models\ORM\Author')->find($k)) {
                    return $app->json(array('message' => 'Not found author id ' . $k));
                }
                $authors[$k] = $app['em']->getRepository('MyApp\Models\ORM\Author')->find($k);
            }
            $book->setAuthor($authors);

            $errors = $app['validator']->validate($book);
            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);
            } else {
                $app['em']->flush();
                $book_id = $book->getBookId();
                return $app->json(array('message' => 'The book id ' . $book_id . ' updated'), 204);

            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function deleteBook(Application $app, $id)
    {
        try {
            $book = $app['em']->getRepository('MyApp\Models\ORM\Book')
                ->find($id);
            if (!$book) {
                $error = array('message' => 'Not found book id ' . $id);
                return $app->json($error, 404);
            }
            $app['em']->remove($book);
            $app['em']->flush();
            return $app->json(array('message' => 'The book id ' . $id . ' deleted'), 200);
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
    }
}
