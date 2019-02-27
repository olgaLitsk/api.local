<?php
namespace Tests\Controllers;

use Silex\WebTestCase;

class IndexPageTest extends WebTestCase
{
    public function createApplication()
    {
        $app_env = 'test';
        $app = require __DIR__ . '/../../../web/index.php';
        return $app;
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url, [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo']);
        $this->assertTrue($client->getResponse()->isSuccessful());
//        $this->assertJson($client->getResponse()->getContent());
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
//        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
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
        $client = $this->createClient();
        $client->request(
            'GET',
            '/authors/',
            [],
            [],
            ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo']
        );
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
//        $this->assertJson($client->getResponse()->getContent());
    }

    public function test_showActionId()
    {
        $client = $this->createClient();
        $client->request(
            'GET',
            '/authors/1',
            [],
            [],
            ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo']
        );
        $json = '[{"author_id": 1,"firstname": "Андрей","lastname": "Курпатов","about": "Андрей Курпатов – врач-психотерапевт, президент Высшей школы методологии, основатель и ведущий интеллектуального ток-шоу «Игры разума», автор более сотни научных работ, создатель системной поведенческой психотерапии и методологии мышления."
    }]';
        $response = $client->getResponse();
        $data = $response->getContent();
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
//        $this->assertJson($data);
//        $this->assertJsonStringEqualsJsonString($data, $json);
    }

    public function test_createAction()
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/authors/',
            [],
            [],
            ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'foo'],
            '{"firstname": "Дэн",
        "lastname": "Браун",
        "about": "Дэн Браун – американский писатель и журналист, чьи произведения переводились на пятьдесят языков и издавались многомиллионными тиражами в различных странах мира."}'
        );
        $response = $client->getResponse();
        $data = $response->getContent();
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue(201 === $client->getResponse()->getStatusCode());
    }

//    public function test_deleteAction()
//    {
//        $client = $this->createClient();
//
//        $client->request(
//            'DELETE',
//            '/authors/3',
//            [],
//            [],
//            []
//        );
//        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
//    }

}