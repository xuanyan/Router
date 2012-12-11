<?php

class blogController extends Controller
{
    function indexAction()
    {
        return $this->router->module;
    }
}