<?php

use \Framework\Response;
use Framework\Exception\FrameworkException;

class ResponseTest extends PHPUnit_Framework_TestCase {

    public function testJson( ) {
        $sample = Response::json(
            array('foo'=>'bar'),
            404);

        $this->assertEquals(404, $sample->getCode());


        $this->assertContains(
            "Content-type: application/json",
            $sample->getHeaders());

        $this->assertEquals(
            json_encode(array('foo'=>'bar')),
            $sample->getContent());

    }

    public function handleExceptionTest( ) {
        $e = new FrameworkException("message", 500);
        //TODO: Exception Handler implementation

    }

    public function redirectTest( ) {
        $r = Response::redirect('/index.php', 301);
        $this->assertContains('Location: /index.php', $r->getHeaders());
        $this->assertEquals(301, $r->getCode());
        $r = Response::redirect('/index.php');
        $this->assertEquals(302, $r->getCode());
    }

    public function setCodeTest() {

        $r = (new Response())->setCode(200);
        $this->assertContains("HTTP/1.0 200 OK", $r->getHeaders());
        $r = (new Response())->setCode(301);
        $this->assertContains("HTTP/1.0 301 Moved Permanently", $r->getHeaders());
        $r = (new Response())->setCode(302);
        $this->assertContains("HTTP/1.0 302 Found", $r->getHeaders());
        $r = (new Response())->setCode(400);
        $this->assertContains("HTTP/1.0 400 Bad Request", $r->getHeaders());
        $r = (new Response())->setCode(401);
        $this->assertContains("HTTP/1.0 401 Bad Unauthorised", $r->getHeaders());
        $r = (new Response())->setCode(403);
        $this->assertContains("HTTP/1.0 403 Forbidden", $r->getHeaders());
        $r = (new Response())->setCode(404);
        $this->assertContains("HTTP/1.0 404 Not Found", $r->getHeaders());
        $r = (new Response())->setCode(500);
        $this->assertContains("HTTP/1.0 500 Internal Server Error", $r->getHeaders());
    }

    public function displayTest( ) {
        //TODO: how to work on Display test
    }
} 