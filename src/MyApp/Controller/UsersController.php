<?php
namespace MyApp\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
class UsersController{

    public function index(Application $app){
        // show the list of users
        $sql = "SELECT * FROM customers";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'The user was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function show(Application $app, $id){
        // show the user #id
        $sql = "SELECT * FROM customers WHERE customer_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The user was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post,200);
    }

    public function store(Application $app,Request $request){
        // create a new user, using POST method

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $constraint = new Assert\Collection(array(
            'firstname' => new Assert\NotBlank(),
            'lastname'  => new Assert\NotBlank(),
            'email' => new Assert\Email(),
            'phonenumber' => new Assert\NotBlank(),
        ));

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $phoneChecked = self::CurlPhoneValidation($parametersAsArray['phonenumber']);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
        }
        if($phoneChecked == false){
            $errs_msg['errors']['[phonenumberInternational]'] = 'This value is not a valid phone number format.';
        }
        if (count($errors)>0 or $phoneChecked == false){
            return new Response(json_encode($errs_msg),404);
        }
        else{
            $parametersAsArray['phonenumber'] = $phoneChecked;
            $app['db']->insert('customers', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            return $app->redirect('/users/list/' . $lastInsertId, 201);
        }
    }

    public function update(Application $app,Request $request, $id){
        // update the user #id, using PUT method
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = [];
        if (isset($parametersAsArray['firstname'])) $constraintArr['firstname'] = new Assert\NotBlank();
        if (isset($parametersAsArray['lastname'])) $constraintArr['lastname'] = new Assert\NotBlank();
        if (isset($parametersAsArray['email'])) $constraintArr['email'] = new Assert\Email();
        if (isset($parametersAsArray['phonenumber'])) $constraintArr['phonenumber'] = new Assert\NotBlank();


        $constraint = new Assert\Collection($constraintArr);

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $phoneChecked = self::CurlPhoneValidation($parametersAsArray['phonenumber']);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
        }
        if($phoneChecked == false){
            $errs_msg['errors']['[phonenumberInternational]'] = 'This value is not a valid phone number format.';
        }
        if (count($errors)>0 or $phoneChecked == false){
            return new Response(json_encode($errs_msg),404);
        }else{
            $parametersAsArray['phonenumber'] = $phoneChecked;
            $app['db']->update('customers', $parametersAsArray, array('customer_id' => $id));

        }

        return new Response('Customer updated',200);

    }

    public function destroy(Application $app, $id){
        // delete the user #id, using DELETE method
        try {
            $sql = "SELECT * FROM customers WHERE customer_id = ?";
            $userInfo = $app['db']->fetchAssoc($sql, array($id));

            if (!$userInfo)
                return new Response('Customer not found', 404);

            $app['db']->delete('customers', array(
                    'customer_id' => $userInfo['customer_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
        return new Response('Custormer Deleted', 200);
    }

    public function CurlPhoneValidation($phone){
        // set API Access Key
        $access_key = '9903d695c5953b3b26aa028e9f853912';

        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/validate?access_key='.$access_key.'&number='.$phone.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);
        $validationResult = json_decode($json, true);
        if (!$validationResult['valid']){
            return false;
        }else{
            return $validationResult['international_format'];
        }
    }

    public function getCustomerOrders(Application $app, $id){
        //list of orders customer #id
        $sql = "SELECT * FROM orders
                WHERE customer=?";
        $post = $app['db']->fetchAll($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The books were not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }
}
