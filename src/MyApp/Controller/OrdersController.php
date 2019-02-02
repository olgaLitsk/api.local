<?php
namespace MyApp\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
class OrdersController
{
    public function ordersGet(Application $app){//нужно ли join-ть с т покупателей
        $sql = "SELECT * FROM orders";
        $post = $app['db']->fetchAll($sql);
        if (!$post) {
            $error = array('message' => 'The order was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post, 200);
    }

    public function ordersIdGet(Application $app, $id){//нужно ли join-ть с т покупателей, книг
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $post = $app['db']->fetchAssoc($sql, array((int) $id));
        if (!$post) {
            $error = array('message' => 'The order was not found.');
            return $app->json($error, 404);
        }
        return $app->json($post,200);
    }

    public function ordersPost(Application $app,Request $request){
        $parametersAsArray = array();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraint = new Assert\Collection(array(
            'orderdate' => new Assert\Type('string'),
            'customer'  => new Assert\Type('integer'),
            'status' => new Assert\Type('string'),
        ));

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $errs_msg = array();
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return new Response(json_encode($errs_msg),404);
        }else{
            $app['db']->insert('orders', $parametersAsArray);
            $lastInsertId = $app['db']->lastInsertId();
            return $app->redirect('/orders/list/' . $lastInsertId, 201);
        }
    }

    public function ordersPut(Application $app,Request $request, $id){
        $parametersAsArray = array();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $constraintArr = array();
        if (isset($parametersAsArray['orderdate'])) $constraintArr['orderdate'] = new Assert\Type('string');
        if (isset($parametersAsArray['customer'])) $constraintArr['customer'] = new Assert\Type('integer');
        if (isset($parametersAsArray['status'])) $constraintArr['status'] = new Assert\Type('string');

        $constraint = new Assert\Collection($constraintArr);

        $errors = $app['validator']->validate($parametersAsArray, $constraint);

        $errs_msg = array();
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return new Response(json_encode($errs_msg),404);
        }else{
            $app['db']->update('orders', $parametersAsArray, array('order_id' => $id));
        }
        return new Response('order updated',200);
    }

    public function ordersDelete(Application $app, $id){
        try {
            $sql = "SELECT * FROM orders WHERE order_id = ?";
            $orderInfo = $app['db']->fetchAssoc($sql, array($id));

            if (!$orderInfo)
                return new Response('order not found', 404);

            $app['db']->delete('orders', array(
                    'order_id' => $orderInfo['order_id'],
                )
            );
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
        return new Response('Custormer Deleted', 200);
    }
}
