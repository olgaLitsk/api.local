<?php
use Silex\WebTestCase;
use MyApp\Controllers\AuthorsController;

class IndexPageTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../web/index.php';
        $app['debug'] = true;
        unset($app['exeption_handler']);
        return $app;
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertJson($client->getResponse()->getContent());

    }

    public function provideUrls()
    {
        return [
            ['/authors/'],
            ['/books/'],
            ['/users/'],
            ['/orders/']
        ];
    }

    public function test_showAction()
    {
        $client = self::createClient();
        $client->request('GET', '/authors/');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertJson($client->getResponse()->getContent());

    }

    public function test_showActionId()
    {
        $client = $this->createClient();
        $client->request('GET', '/authors/1');
        $json = '[{"author_id": 1,"firstname": "Андрей","lastname": "Курпатов","about": "Андрей Курпатов – врач-психотерапевт, президент Высшей школы методологии, основатель и ведущий интеллектуального ток-шоу «Игры разума», автор более сотни научных работ, создатель системной поведенческой психотерапии и методологии мышления."
    }]';
        $response = $client->getResponse();
        $data = $response->getContent();
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $this->assertJson($data);
        $this->assertJsonStringEqualsJsonString($data, $json);

    }

    public function test_createAction()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/authors/',
            [],
            [],
            [],
            '{"firstname": "Дэн",
        "lastname": "Браун",
        "about": "Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира."}'
        );
        $response = $client->getResponse();
        $data = $response->getContent();
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue(201 === $client->getResponse()->getStatusCode());
//        $this->assertJson($data);
    }

    public function test_deleteAction()
    {
        $client = $this->createClient();

        $client->request(
            'DELETE',
            '/authors/3',
            [],
            [],
            []
        );
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
    }

}