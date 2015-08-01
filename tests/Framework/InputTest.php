<?php

use \Framework\Input;
use \Framework\Exception\FrameworkException;
use \Framework\Validator\IsNumericValidator;

class InputTest extends PHPUnit_Framework_TestCase {

    public function testGet( ) {

        //basic
        Input::set('key', '123');
        $this->assertEquals(123, Input::get('key'));

        //default value
        $this->assertEquals(123, Input::get('key2', 123) );

        //default value by function-based default value
        $this->assertEquals(100,
            Input::get('key2', function(){
                return 100;
            }));

        //default value by throwing Exception explicitly
        $exception = null;
        try {
            $value = Input::get('key2', function(){
                throw new FrameworkException("sample", 500);
            });
        } catch ( Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf(
            '\Framework\Exception\FrameworkException',
            $exception);

        try {
            $value = Input::get('key', function(){
                throw new FrameworkException("sample", 500);
            });
        } catch ( Exception $e) {
            $value = $e;
        }
        $this->assertEquals(123, $value);

        //default value by specifying Framework Exception

        $exception = null;
        try {
            Input::get("key2", new FrameworkException());
        } catch ( Exception $e ) {
            $exception = $e;
        }
        $this->assertInstanceOf(
            '\Framework\Exception\FrameworkException',
            $exception);

        //Validator

        $value = Input::get('key', -1, function($k){
            if ( is_numeric($k) ) return true;
            return false;
        });
        $this->assertEquals(123, $value);

        $value = Input::get('key2', 123, function($k){
            if ( is_numeric($k) ) return true;
            return false;
        });
        $this->assertEquals(123, $value);

        Input::set("key3", "abc");
        $value = Input::get('key3', 123, function($k){
            if ( is_numeric($k) ) return true;
            return false;
        });
        $this->assertEquals(123, $value);

        $exception = null;
        try {
            Input::get('key3',
                FrameworkException::lackParameter('key 3'),
                function($k){
                if ( is_numeric($k) ) return true;
                return false;
            });
        } catch ( Exception $e ) {
            $exception = $e;
        }
        $this->assertInstanceOf(
            '\Framework\Exception\FrameworkException',
            $exception);


        $value = Input::get('key', -1, new IsNumericValidator() );
        $this->assertEquals(123, $value);

        $value = Input::get('key3', -1, new IsNumericValidator() );
        $this->assertEquals(-1, $value);


    }
}