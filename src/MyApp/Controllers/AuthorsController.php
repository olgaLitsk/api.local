<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\AuthorsModel;
use MyApp\Models\ORM\Authors;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorsController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authors = $app["controllers_factory"];
        $authors->get("/", "MyApp\\Controllers\\AuthorsController::authorsGet");    // вывод списка авторов
        $authors->post("/", "MyApp\\Controllers\\AuthorsController::authorsPost");    // добавление нового автора
        $authors
            ->get("/{id}", "MyApp\\Controllers\\AuthorsController::authorsIdGet")    // вывод инф-ии об авторе
            ->assert ('id', '\d+');
        $authors
            ->put("/{id}", "MyApp\\Controllers\\AuthorsController::authorsIdPut")    // обновление данных автора
            ->assert ('id ', '\d+');
        $authors
            ->delete("/{id}", "MyApp\\Controllers\\AuthorsController::authorsIdDelete")    // удаление автора
            ->assert ('id ', '\d+ ');

        // доп-но
        $authors
            ->get("/{id}/books","MyApp\\Controllers\\AuthorsController::authorsIdBooksGet")//вывод списка книг, принадлежащих автору с #id
            ->assert ('id ', '\d+ ');//+
        return $authors;
    }

    public function authorsGet(Application $app)
    {
        try {
            $data = new AuthorsModel();
            $post= $data->authorsGet($app);
            return $app->json($post, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function authorsIdGet(Application $app, $id)
    {
        try {
            $data = new AuthorsModel();
            $post= $data->authorsIdGet($app, $id);
            return $app->json($post, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function authorsPost(Application $app, Request $request)
    {
        try {
            $content = json_decode($request->getContent(),true);

            $ormPost = new Authors();
            $ormPost->setFirstname($content['firstname']);
            $ormPost->setLastname($content['lastname']);
            $ormPost->setAbout($content['about']);

            $errors = $app['validator']->validate($ormPost);
            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);
            } else {
                $data = new AuthorsModel();
                $post = $data->authorsPost($app, $content);
                return $app->redirect('/authors/' . $post, 201);
            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function authorsIdPut(Application $app, Request $request, $id)
    {
        try {
            $content = json_decode($request->getContent(),true);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }

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
            $data = new AuthorsModel();
            $authorInfo= $data->authorsIdGet($app, $id);

            if (!$authorInfo)
                return new Response('author not found', 404);

            $app['db']->delete('authors', array(
                    'author_id' => $authorInfo['author_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }

        return $app->json($authorInfo);
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
