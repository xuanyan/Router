<?php

/*
* Controller abstract class
*
* @copyright (c) 2012 Atom Projects More info http://Atom.com
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
* @author xuanyan <yxuan@myspace.cn>
*
*/

abstract class Controller
{
    protected $router = null;

    function __construct($router)
    {
        $this->router = $router;
    }

    protected function isPost()
    {
        return issset($_SERVER['REQUEST_METHOD']) ? 'POST' == $_SERVER['REQUEST_METHOD'] : false;
    }

    protected function getClientIp() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
             $onlineip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
             $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
             $onlineip = $_SERVER['REMOTE_ADDR'];
        } else {
            return 'unknown';
        }

        return filter_var($onlineip, FILTER_VALIDATE_IP) !== false ? $onlineip : 'unknown';
    }

    protected function _GET($key = '', $default = '') {
        if (empty($key)) {
            return $_GET;
        }
        if (!isset($_GET[$key])) {
            return $default;
        }
        if (is_string($default)) {
            return trim($_GET[$key]);
        }
        if (is_int($default)) {
            return intval($_GET[$key]);
        }
        if (is_array($default)) {
            return (array)$_GET[$key];
        }
        return floatval($_GET[$key]);
    }

    protected function _POST($key = '', $default = '') {
        if (empty($key)) {
            return $_POST;
        }
        if (!isset($_POST[$key])) {
            return $default;
        }
        if (is_string($default)) {
            return trim($_POST[$key]);
        }
        if (is_int($default)) {
            return intval($_POST[$key]);
        }
        if (is_array($default)) {
            return (array)$_POST[$key];
        }
        return floatval($_POST[$key]);
    }
}

?>