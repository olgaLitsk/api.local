<?php

namespace Tests\Controllers;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use MyApp\Controllers\AuthorsController;
use PHPUnit\Framework\TestCase;

class AuthorsControllerTest extends TestCase
{
    private $author;
    public function setUp()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            "db.options" => array(
                "driver" => "pdo_sqlite",
                "memory" => true
            ),
        ));
    }

    public function testGetOne()
    {
        $this->author = new AuthorsController();

        $data = $this->author->showActionId(1);
        $this->assertEquals('dummyfirstname', $data['firstname']);
    }

//    public function testGetAll()
//    {
//        $data = $this->author->getAll();
//        $this->assertNotNull($data);
//    }
//
//    function testSave()
//    {
//        $firstname = array("firstname" => "arny");
//        $data = $this->author->save($firstname);
//        $data = $this->author->getAll();
//        $this->assertEquals(2, count($data));
//    }
//
//    function testUpdate()
//    {
//        $firstname = array("firstname" => "arny1");
//        $this->author->save($firstname);
//        $firstname = array("firstname" => "arny2");
//        $this->author->update(1, $firstname);
//        $data = $this->author->getAll();
//        $this->assertEquals("arny2", $data[0]["firstname"]);
//
//    }
//
//    function testDelete()
//    {
//        $firstname = array("firstname" => "arny1");
//        $this->author->save($firstname);
//        $this->author->delete(1);
//        $data = $this->author->getAll();
//        $this->assertEquals(1, count($data));
//    }

}
