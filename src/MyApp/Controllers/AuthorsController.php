<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\Author;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthorsController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/", "MyApp\\Controllers\\AuthorsController::showAction");    // вывод списка авторов
        $authors
            ->post("/", "MyApp\\Controllers\\AuthorsController::createAction")// добавление нового автора
            ->before(function () use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
                    throw new AccessDeniedException('Access Denied.');
                }
            });
        $authors
            ->get("/{id}", "MyApp\\Controllers\\AuthorsController::showActionId")// вывод инф-ии об авторе
            ->assert('id', '\d+');
        $authors
            ->put("/{id}", "MyApp\\Controllers\\AuthorsController::updateAction")// обновление данных автора
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+');
        $authors
            ->delete("/{id}", "MyApp\\Controllers\\AuthorsController::deleteAction")// удаление автора
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+ ');
        // доп-но
        $authors
            ->get("/{id}/books", "MyApp\\Controllers\\AuthorsController::authorsIdBooksGet")//вывод списка книг, принадлежащих автору с #id
            ->assert('id ', '\d+ ');
        return $authors;
    }

    public function showAction(Application $app)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Author');
            $query = $repository->createQueryBuilder('a')->getQuery();
            $authors = $query->getArrayResult();
            return $app->json($authors, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function showActionId(Application $app, $id)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Author');
            $query = $repository->createQueryBuilder('a')
                ->where('a.author_id = :identifier')
                ->setParameter('identifier', $id)
                ->getQuery();
            $authors = $query->getArrayResult();

            if (!$authors) {
                $error = array('message' => 'Not found author for id ' . $id);
                return $app->json($error, 404);
            }
            return $app->json($authors, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function createAction(Application $app, Request $request)
    {
        try {
        $content = json_decode($request->getContent(), true);
        $author = new Author();
        $author->setFirstname($content['firstname']);
        $author->setLastname($content['lastname']);
        $author->setAbout($content['about']);

        $errors = $app['validator']->validate($author);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg, 404);
        } else {
            $app['em']->persist($author);
            $app['em']->flush();
            $author_id = $author->getAuthorId();
            return $app->redirect('/authors/' . $author_id, 201);
        }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        try {
            $content = json_decode($request->getContent(), true);
            $author = $app['em']->getRepository('MyApp\Models\ORM\Author')
                ->find($id);
            if (!$author) {
                $error = array('message' => 'Not found author id ' . $id);
                return $app->json($error, 404);
            }
            $author->setFirstname($content['firstname']);
            $author->setLastname($content['lastname']);
            $author->setAbout($content['about']);

            $errors = $app['validator']->validate($author);
            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);
            } else {
                $app['em']->flush();
                $author_id = $author->getAuthorId();
                return $app->json(array('message' => 'The author id ' . $author_id . ' updated'), 200);
            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function deleteAction(Application $app, $id)
    {
        try {
            $author = $app['em']->getRepository('MyApp\Models\ORM\Author')
                ->find($id);
            if (!$author) {
                $error = array('message' => 'Not found author id ' . $id);
                return $app->json($error, 404);
            }
            $app['em']->remove($author);
            $app['em']->flush();
            return $app->json(array('message' => 'The author id ' . $id . ' deleted'), 200);
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
    }

//    public function authorsIdBooksGet(Application $app, $id)
//    {
//        //list of books author #id - фича
//        $sql = "SELECT * FROM books as b
//                LEFT JOIN authors_books as ab ON b.book_id = ab.book
//                LEFT JOIN authors as a ON a.author_id = ab.author
//                WHERE author=?";
//        $post = $app['db']->fetchAll($sql, array((int)$id));
//        if (!$post) {
//            $error = array('message' => 'The books were not found.');
//            return $app->json($error, 404);
//        }
//        return $app->json($post, 200);
//    }

}
