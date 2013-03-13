<?php

require_once dirname(__DIR__) . '/src/Router.php';

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
        $this->assertEquals('blog', $this->router->run('blog/blog'));
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
    
    public function testEight() {
        // catch not exists controller to do a time redirect controller
        try {
            $this->router->run('no_exists');
        } catch (RouterException $e) {
            $this->assertEquals('no_exists', $this->router->run('test/test/'.$this->router->controller));
        }
    }

    public function testNine() {
        $this->router->map('getUserName/:username', 'test/test/:username');
        $this->assertEquals('xuanyan', $this->router->run('getUserName/xuanyan'));
    }

    public function testTen() {
        $this->router->map('blog/:username', 'test/test/:username');
        $this->assertEquals('blog', $this->router->run('blog/blog'));
    }

    public function tearDown()
    {
        $this->router = null;
    }
}