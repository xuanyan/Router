<?php

require_once dirname(__DIR__) . '/src/Router.php';
require_once dirname(__DIR__) . '/src/Controller.php';

class routerTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->router = new Router(__DIR__ . '/controllers');
        $this->router->setModule('blog', __DIR__ . '/blog');
    }

    public function testOne() {
        $this->assertEquals('ok', $this->router->run('/'));
    }

    public function testTwo() {
        $this->assertEquals('abc', $this->router->run('test/test/abc'));
    }
    
    public function testThree() {
        $_GET['a'] = 'abc';
        $this->assertEquals('abc', $this->router->run('test/test'));
    }

    public function testFour() {
        $this->assertEquals('ok', $this->router->run('test'));
    }
    
    public function testFive() {
        $this->assertEquals('blog', $this->router->run('blog'));
    }

    public function testSix() {
        $this->assertEquals('abc', $this->router->run('admin/admin/test/a/b/c'));
    }
    
    /**
     * @expectedException RouterException
     */
    public function testSeven() {
        $this->router->run('no_exists/no_exists');
    }

    public function tearDown()
    {
        $this->router = null;
    }
}