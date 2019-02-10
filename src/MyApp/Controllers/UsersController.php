<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\User;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UsersController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $users = $app["controllers_factory"];
        $users->get("/", "MyApp\\Controllers\\UsersController::showAction");// вывод списка покупателей
        $users->post("/", "MyApp\\Controllers\\UsersController::usersPost");// добавление покупателя
        $users
            ->get("/{id}", "MyApp\\Controllers\\UsersController::showActionId")// вывод инф-ии о покупателе
            ->assert('id', '\d+');
        $users
            ->put("/{id}", "MyApp\\Controllers\\UsersController::usersIdPut")// обновление данных покупателя
            ->assert('id ', '\d+');
        $users
            ->delete("/{id}", "MyApp\\Controllers\\UsersController::usersIdDelete")// удаление покупателя
            ->assert('id ', '\d+ ');

        $users
            ->get("/{id}/orders", "MyApp\\Controllers\\UsersController::usersIdOrdersGet")// вывод заказов покупателя id
            ->assert('id ', '\d+ ');

        return $users;
    }

    public function showAction(Application $app)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\User');
            $query = $repository->createQueryBuilder('u')->getQuery();
            $users = $query->getArrayResult();
            return $app->json($users, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function showActionId(Application $app, $id)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\User');
            $query = $repository->createQueryBuilder('u')
                ->where('u.user_id = :identifier')
                ->setParameter('identifier', $id)
                ->getQuery();
            $users = $query->getArrayResult();

            if (!$users) {
                $error = array('message' => 'Not found user for id ' . $id);
                return $app->json($error, 404);
            }
            return $app->json($users, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function usersPost(Application $app, Request $request)
    {
//        try {
            $content = json_decode($request->getContent(), true);
            $user = new User();
            $user->setFirstname($content['firstname']);
            $user->setLastname($content['lastname']);
            $user->setEmail($content['email']);
            $user->setPhonenumber($content['phonenumber']);
            $user->setRoles($content['roles']);
            $user->setUsername($content['username']);
            $user->setPassword($content['password']);

            $errors = $app['validator']->validate($user);
            $phoneChecked = $app['phone.service']->CurlPhoneValidation($content['phonenumber']);

            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);
            }
            if ($phoneChecked == false) {
                $errs_msg['errors']['[phonenumberInternational]'] = 'This value is not a valid phone number format.';
            }
            if (count($errors) > 0 or $phoneChecked == false) {
                return $app->json($errs_msg, 404);
            } else {
                $app['em']->persist($user);
                $app['em']->flush();
                $user_id = $user->getUserId();
                return $app->redirect('/authors/' . $user_id, 201);
            }
//        } catch (\Exception $e) {
//            return $app->json($e, 404);
//        }
    }

    public function usersIdPut(Application $app, Request $request, $id)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = [];
        if (isset($parametersAsArray['firstname'])) $constraintArr['firstname'] = new Assert\NotBlank();
        if (isset($parametersAsArray['lastname'])) $constraintArr['lastname'] = new Assert\NotBlank();
        if (isset($parametersAsArray['email'])) $constraintArr['email'] = new Assert\Email();
        if (isset($parametersAsArray['phonenumber'])) $constraintArr['phonenumber'] = new Assert\NotBlank();
        if (isset($parametersAsArray['role'])) $constraintArr['role'] = new Assert\NotBlank();
        if (isset($parametersAsArray['user_login'])) $constraintArr['user_login'] = new Assert\NotBlank();
        if (isset($parametersAsArray['user_password'])) $constraintArr['user_password'] = new Assert\NotBlank();

        $constraint = new Assert\Collection($constraintArr);

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $phoneChecked = $app['phone.service']->CurlPhoneValidation($parametersAsArray['phonenumber']);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
        }
        if ($phoneChecked == false) {
            $errs_msg['errors']['[phonenumberInternational]'] = 'This value is not a valid phone number format.';
        }
        if (count($errors) > 0 or $phoneChecked == false) {
            return $app->json($errs_msg, 404);
        } else {
            $parametersAsArray['phonenumber'] = $phoneChecked;
            $app['db']->update('users', $parametersAsArray, array('user_id' => $id));
        }
        return $app->json('user updated', 200);
    }

    public function usersIdDelete(Application $app, $id)
    {
        try {
            $user = $app['em']->getRepository('MyApp\Models\ORM\User')
                ->find($id);
            if (!$user) {
                $error = array('message' => 'Not found user id ' . $id);
                return $app->json($error, 404);
            }
            $app['em']->remove($user);
            $app['em']->flush();
            return $app->json(array('message' => 'The user id ' . $id . ' deleted'), 200);
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
    }

//    public function usersIdOrdersGet(Application $app, $id)//фича
//    {
//        $sql = "SELECT * FROM orders
//                WHERE user=?";
//        $post = $app['db']->fetchAll($sql, array((int)$id));
//        if (!$post) {
//            $error = array('message' => 'The books were not found.');
//            return $app->json($error, 404);
//        }
//        return $app->json($post, 200);
//    }
}