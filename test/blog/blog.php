<?php

class blogController
{
    private $router = null;
    
    function __construct($router)
    {
        $this->router = $router;
    }

    function indexAction()
    {
        return $this->router->module;
    }
}