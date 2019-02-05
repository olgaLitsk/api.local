<?php
namespace MyApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UsersController
{
    public function usersGet(Application $app)
    {
        $sql = "SELECT * FROM users";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'The user was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function usersIdGet(Application $app, $id)
    {
        // show the user #id
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int)$id));
        if (!$post) {
            $error = array('message' => 'The user was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function usersPost(Application $app, Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $constraint = new Assert\Collection(array(
            'firstname' => new Assert\NotBlank(),
            'lastname' => new Assert\NotBlank(),
            'email' => new Assert\Email(),
            'phonenumber' => new Assert\NotBlank(),
            'role' => new Assert\NotBlank(),
            'user_login'  => new Assert\NotBlank(),
            'user_password' => new Assert\NotBlank(),

        ));

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
            $app['db']->insert('users', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            return $app->redirect('/users/list/' . $lastInsertId, 201);
        }
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
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $userInfo = $app['db']->fetchAssoc($sql, array($id));

            if (!$userInfo)
                return $app->json('user not found', 404);

            $app['db']->delete('users', array(
                    'user_id' => $userInfo['user_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
        return $app->json('user deleted', 200);

    }

    public function usersIdOrdersGet(Application $app, $id)
    {
        $sql = "SELECT * FROM orders
                WHERE user=?";
        $post = $app['db']->fetchAll($sql, array((int)$id));
        if (!$post) {
            $error = array('message' => 'The books were not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }
}

