<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\Order;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrdersController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $orders = $app["controllers_factory"];
        $orders->get("/", "MyApp\\Controllers\\OrdersController::showAction");// вывод списка заказов
        $orders->post("/", "MyApp\\Controllers\\OrdersController::ordersPost");// создание заказа
        $orders
            ->get("/{id}", "MyApp\\Controllers\\OrdersController::showActionId")// вывод данных по заказу
            ->assert ('id', '\d+');
        $orders
            ->put("/{id}", "MyApp\\Controllers\\OrdersController::ordersPut") // обновление данных по заказу
            ->assert ('id ', '\d+');
        $orders
            ->delete("/{id}", "MyApp\\Controllers\\OrdersController::ordersDelete")// удаление заказа
            ->assert ('id ', '\d+ ');
//-
        $orders
            ->get("/books/{id}", "MyApp\\Controllers\\OrdersController::ordersIdBooksGet") //книги принадлежащие заказу #id
            ->assert ('id ', '\d+ ');

        return $orders;
    }

    public function showAction(Application $app)
    {
//        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Order');
            $query = $repository->createQueryBuilder('o')->getQuery();
            $orders = $query->getArrayResult();
            return $app->json($orders, 200);
//        } catch (\Exception $e) {
//            return $app->json($e, 404);
//        }
    }

    public function showActionId(Application $app, $id)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Order');
            $query = $repository->createQueryBuilder('o')
                ->where('o.order_id = :identifier')
                ->setParameter('identifier', $id)
                ->getQuery();
            $orders = $query->getArrayResult();

            if (!$orders) {
                $error = array('message' => 'Not found order for id ' . $id);
                return $app->json($error, 404);
            }

            return $app->json($orders, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
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
            $author = $app['em']->getRepository('MyApp\Models\ORM\Order')
                ->find($id);
            if (!$author) {
                $error = array('message' => 'Not found order id ' . $id);
                return $app->json($error, 404);
            }
            $app['em']->remove($author);
            $app['em']->flush();
            return $app->json(array('message' => 'The order id ' . $id . ' deleted'), 200);
        } catch (\Exception $e) {
            return new Response(json_encode($e->getMessage()), 404);
        }
    }
}