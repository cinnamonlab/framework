<?php

use \Framework\Route;
use \Framework\Response;

class RouteTest extends PHPUnit_Framework_TestCase {

    /**
     * @runInSeparateProcess
     */
    function testAction( ) {

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';

        $this->assertNotTrue( Route::action('GET', '/nothello',
            function(){return (new Response());}));

        $this->assertNotTrue( Route::action('GET', '/me/hello',
            function(){return (new Response());}));

        $this->assertNotTrue( Route::action('GET', '/hello/me',
            function(){return (new Response());}));

        $this->assertTrue( Route::action('GET', '/hello',
            function(){return (new Response());}));

        $this->assertNotTrue( Route::action('GET', '/hello',
            function(){return (new Response());}));

        Route::reset();

        $_SERVER['REQUEST_URI'] = '/hello?a=b';

        $this->assertTrue( Route::action('GET', '/hello',
            function(){return (new Response());}));

        Route::reset();

    }

    /**
     * @runInSeparateProcess
     */
    function testGet( ) {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';
        $this->assertNotTrue( Route::get('/hello',
            function(){return (new Response());}));
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue( Route::get('/hello',
            function(){return (new Response());}));
    }
    /**
     * @runInSeparateProcess
     */
    function testPost( ) {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';
        $this->assertNotTrue( Route::post('/hello',
            function(){return (new Response());}));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue( Route::post('/hello',
            function(){return (new Response());}));
    }
    /**
     * @runInSeparateProcess
     */
    function testPatch( ) {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';
        $this->assertNotTrue( Route::patch('/hello',
            function(){return (new Response());}));
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $this->assertTrue( Route::patch('/hello',
            function(){return (new Response());}));
    }

    /**
     * @runInSeparateProcess
     */
    function testPut( ) {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';
        $this->assertNotTrue( Route::put('/hello',
            function(){return (new Response());}));
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->assertTrue( Route::put('/hello',
            function(){return (new Response());}));
    }
    /**
     * @runInSeparateProcess
     */
    function testDelete( ) {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/hello';
        $this->assertNotTrue( Route::delete('/hello',
            function(){return (new Response());}));
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->assertTrue( Route::delete('/hello',
            function(){return (new Response());}));
    }

} 