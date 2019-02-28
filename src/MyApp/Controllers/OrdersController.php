<?php
namespace MyApp\Controllers;

use Silex\Application;
use MyApp\Models\ORM\Order;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrdersController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $orders = $app["controllers_factory"];
        $orders->get("/", "MyApp\\Controllers\\OrdersController::showAction");// вывод списка заказов
        $orders->post("/", "MyApp\\Controllers\\OrdersController::ordersPost");// создание заказа юзерами
        $orders
            ->get("/{id}", "MyApp\\Controllers\\OrdersController::showActionId")// вывод данных по заказу
            ->assert('id', '\d+');
        $orders
            ->put("/{id}", "MyApp\\Controllers\\OrdersController::ordersPut")// обновление данных по заказу
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+');
        $orders
            ->delete("/{id}", "MyApp\\Controllers\\OrdersController::ordersDelete")// удаление заказа
            ->before(function (Request $request) use ($app) {
                if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN', $request->get('id'))) {
                    throw new AccessDeniedException('Access Denied.');
                }
            })
            ->assert('id ', '\d+ ');
        return $orders;
    }

    public function showAction(Application $app)
    {
        try {
            $repository = $app['em']->getRepository('MyApp\Models\ORM\Order');
            $query = $repository->createQueryBuilder('o')->getQuery();
            $orders = $query->getArrayResult();
            return $app->json($orders, 200);
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
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

    public function ordersPost(Application $app, Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $user = $app['em']->getRepository('MyApp\Models\ORM\User')
            ->find($content['user']);
        if (!$user) {
            return $app->json(array('message' => 'Not found user id ' . $content['user']));
        }

        $order = new Order();
        $order->setOrderdate($content['orderdate']);
        $order->setStatus($content['status']);
        $order->setUser($user);

        $books = array();
        foreach ($content['books'] as $k) {
            if (!$app['em']->getRepository('MyApp\Models\ORM\Book')->find($k)) {
                return $app->json(array('message' => 'Not found book id ' . $k));
            }
            $books[$k] = $app['em']->getRepository('MyApp\Models\ORM\Book')->find($k);
        }
        $order->setBook($books);
        $errors = $app['validator']->validate($order);
        $errs_msg = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
            return $app->json($errs_msg, 404);
        } else {
            $app['em']->persist($order);
            $app['em']->flush();

            $message = (new \Swift_Message())
                ->setSubject('Order approval')
                ->setFrom(array('litskevich_olga@mail.ru'))
                ->setTo($order->getUser($user)->getEmail())
                ->setBody('Order ' . $order->getStatus());
            $app['mailer']->send($message);

            $order_id = $order->getOrderId();
            return $app->redirect('/orders/' . $order_id, 201);
        }
    }

    public function ordersPut(Application $app, Request $request, $id)
    {
        try {
            $content = json_decode($request->getContent(), true);

            $user = $app['em']->getRepository('MyApp\Models\ORM\User')
                ->find($content['user']);
            if (!$user) {
                return $app->json(array('message' => 'Not found user id ' . $content['user']));
            }
            $order = $app['em']->getRepository('MyApp\Models\ORM\Order')
                ->find($id);
            $order->setOrderdate($content['orderdate']);
            $order->setStatus($content['status']);

            $order->setUser($user);

            $books = array();
            foreach ($content['books'] as $k) {
                if (!$app['em']->getRepository('MyApp\Models\ORM\Book')->find($k)) {
                    return $app->json(array('message' => 'Not found book id ' . $k));
                }
                $books[$k] = $app['em']->getRepository('MyApp\Models\ORM\Book')->find($k);
            }
            $order->setBook($books);

            $errors = $app['validator']->validate($order);
            $errs_msg = [];
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $errs_msg['errors'][$error->getPropertyPath()] = $error->getMessage();
                }
                return $app->json($errs_msg, 404);
            } else {
                $app['em']->flush();
                $order_id = $order->getOrderId();

                $message = (new \Swift_Message())
                    ->setSubject('Order approval')
                    ->setFrom(array('litskevich_olga@mail.ru'))
                    ->setTo($order->getUser($user)->getEmail())
                    ->setBody('Order ' . $order->getStatus());
                $app['mailer']->send($message);

                return $app->json(array('message' => 'Order id ' . $order_id . ' updated'), 204);
            }
        } catch (\Exception $e) {
            return $app->json($e, 404);
        }
    }

    public function ordersDelete(Application $app, $id)
    {
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