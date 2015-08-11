<?php

defined('__DIR__') || define('__DIR__', dirname(__FILE__));

require_once dirname(__DIR__) . '/src/Router.php';



class routerTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->router = new XuanYan\Router(__DIR__ . '/controllers');
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
        } catch (XuanYan\RouterException $e) {
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
    
    public function testEleven() {
        // catch not exists controller/Action , the exception code is 500
        try {
            $this->router->run('index/no_existsAction');
        } catch (XuanYan\RouterException $e) {
            $this->assertEquals('500', $e->getCode());
        }
    }
    
    public function testTwelve() {
        $this->router->map('index/:NUM', 'test/test/:NUM');
        // not rewrite for string
        $this->assertEquals('ok', $this->router->run('index/index'));
        // do rewrite for :NUM
        $this->assertEquals('2', $this->router->run('index/2'));
    }
    
    // test for more then 1 param rewrite
    public function testThirteen() {
        $this->router->map('index/:test/:id', 'test/:test/:id');
        // test/test/test
        $this->assertEquals('test', $this->router->run('index/test/test'));
    }
    
    public function testFourteen() {
        // catch not exists action to do a time redirect controller
        try {
            $this->router->run('index/66778');
        } catch (XuanYan\RouterException $e) {
            $this->assertEquals('ok', $this->router->run('index'));
        }
    }
    
    public function testFiveteen() {
        // make controller running save, it was run only in controlerDir
        try {
            $this->router->run('../routerTest.php');
        } catch (XuanYan\RouterException $e) {
            $this->assertEquals('403', $e->getCode());
        }
    }

    public function testSixteen(){
        // change controller and action name
        $this->router->controllerName = 'Action';
        $this->router->actionName = '%s';
        $this->assertEquals('Action::index', $this->router->run('action'));
    }

    public function tearDown()
    {
        $this->router = null;
    }
}