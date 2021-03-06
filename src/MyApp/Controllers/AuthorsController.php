<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\Author;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Rs\Json\Patch;
use Rs\Json\Patch\InvalidPatchDocumentJsonException;
use Rs\Json\Patch\InvalidTargetDocumentJsonException;
use Rs\Json\Patch\InvalidOperationException;
use Symfony\Component\DomCrawler\Crawler;

class AuthorsController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/", "MyApp\\Controllers\\AuthorsController::getAuthors");    // вывод списка авторов
        $authors
            ->post("/", "MyApp\\Controllers\\AuthorsController::postAuthor")// добавление нового автора
            ->before(function () use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
                    throw new AccessDeniedException('Access Denied.');
                }
            });
        $authors
            ->get("/{id}", "MyApp\\Controllers\\AuthorsController::getAuthor")// вывод инф-ии об авторе
            ->assert('id', '\d+');
        $authors
            ->put("/{id}", "MyApp\\Controllers\\AuthorsController::putAuthor")// обновление данных автора
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+');
        $authors
            ->patch("/{id}", "MyApp\\Controllers\\AuthorsController::patchAuthor")// обновление данных автора
            ->assert('id ', '\d+');
        $authors
            ->delete("/{id}", "MyApp\\Controllers\\AuthorsController::deleteAuthor")// удаление автора
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

    public function getAuthors(Application $app)
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

    public function getAuthor(Application $app, $id)
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

    public function postAuthor(Application $app, Request $request)
    {
        try {
            $content = [];
            if ($request->getContentType() == 'xml') {
                $crawler = new Crawler($request->getContent());
                $content['firstname'] = $crawler->filterXPath('//authors/firstname')->text();
                $content['lastname'] = $crawler->filterXPath('//authors/lastname')->text();
                $content['about'] = $crawler->filterXPath('//authors/about')->text();
            } else {
                $content = json_decode($request->getContent(), true);
            }
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

    public function putAuthor(Application $app, Request $request, $id)
    {
        try {
            $content = [];
            if ($request->getContentType() == 'xml') {
                $crawler = new Crawler($request->getContent());
                $content['firstname'] = $crawler->filterXPath('//authors/firstname')->text();
                $content['lastname'] = $crawler->filterXPath('//authors/lastname')->text();
                $content['about'] = $crawler->filterXPath('//authors/about')->text();
            } else {
                $content = json_decode($request->getContent(), true);
            }
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
                return $app->json(array('message' => 'The author id ' . $author_id . ' updated'), 204);
            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function patchAuthor(Application $app, Request $request, $id)
    {
        try {
            if ($request->getContentType() != 'json') {
                return $app->json(array('message' => 'Unsupported type, expected application/json'), 415);
            }
            $sql = "SELECT * FROM authors WHERE author_id = ?";
            $authors = $app['db']->fetchAssoc($sql, array((int)$id));
            if (!$authors) {
                $error = array('message' => 'The author was not found.');
                return $app->json($error, 404);
            }
            $targetDocument = json_encode($authors);
            $patchDocument = $request->getContent();
            $patch = new Patch($targetDocument, $patchDocument);
            $patchedDocument = $patch->apply();
            $author = $app['em']->getRepository('MyApp\Models\ORM\Author')
                ->find($id);
            $patchedDocument = json_decode($patchedDocument, true);
            $author->setFirstname($patchedDocument['firstname']);
            $author->setLastname($patchedDocument['lastname']);
            $author->setAbout($patchedDocument['about']);
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
                return $app->json(array('message' => 'The author id ' . $author_id . ' updated'), 204);
            }
        } catch (InvalidPatchDocumentJsonException $e) {
            // невалидный json в patch doc
            return $app->json(array('message' => $e->getMessage()), 400);
        } catch (InvalidTargetDocumentJsonException $e) {
            // невалидный json в target doc
            return $app->json(array('message' => $e->getMessage()), 400);
        } catch (InvalidOperationException $e) {
            // невалидный json Pointer operation (отсутствует свойство)
            return $app->json(array('message' => $e->getMessage()), 400);
        }
    }

    public function deleteAuthor(Application $app, $id)
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
}
