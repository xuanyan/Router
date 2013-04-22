<?php

/*
* Router
*
* @copyright (c) 2012 Atom Projects More info http://geek-zoo.com
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
* @author xuanyan <xuanyan@geek-zoo.com>
*
*/

class Router
{
    private $controllerDir = '';
    private $moduleDir = array();
    private $delimiter = '';
    private $rules = array();

    public $format = '';
    public $module = '';
    public $controller = 'index';
    public $action = 'index';
    public $url = '';

    function __construct($controllerDir = 'Controllers', $delimiter = '/')
    {
        $this->controllerDir = $controllerDir;
        $this->delimiter = $delimiter;
    }

    public function map($from, $to)
    {
        if (preg_match_all('/:\w+/i', $from, $matches)) {
            $pattern = array('/:NUM/', '/:\w+/i');
            $replacement = array('(\d+)', '(\w+)');
            $from = preg_replace($pattern, $replacement, $from);
        }
        $this->rules[] = array(
            'pattern' => '%^'.$from.'$%',
            'replacement' => $matches[0],
            'subject' => $to
        );
    }

    public function setModule($module, $controllerDir)
    {
        $this->moduleDir[$module] = $controllerDir;
    }

    private static function getValue($value, $default)
    {
        if (is_string($default)) {
            return trim($value);
        }
        if (is_int($default)) {
            return intval($value);
        }
        if (is_array($default)) {
            return (array)$value;
        }

        return floatval($value);
    }

    /**
     * dispatch url function
     *
     * @param string $url
     * @return mix
     */
    public function run($url)
    {
        $url = $raw_url = trim($url, ' '.$this->delimiter);

        // trim the url extention (xxx/xxx.html or yyy/yyy.asp or any extention)
        if (($pos = strrpos($url, '.')) !== false) {
            $this->format = substr($url, $pos+1);
            $url = substr($url, 0, $pos);
        }

        $this->url = $url;

        $tmp = $url ? array_filter(explode($this->delimiter, $url)) : array();

        $module = current($tmp);

        if (isset($this->moduleDir[$module])) {
            $this->module = array_shift($tmp);
            $path = $this->moduleDir[$module];
        } else {

            // do rewrite
            foreach ($this->rules as $key => $value) {
                if (preg_match_all($value['pattern'], $raw_url, $matches, PREG_SET_ORDER)) {
                    $replace_pairs = array();
                    if ($value['replacement']) {
                        array_shift($matches[0]);
                        $replace_pairs = array_combine($value['replacement'], $matches[0]);
                    }
                    $value['subject'] = strtr($value['subject'], $replace_pairs);
                    unset($this->rules[$key]);

                    return $this->run($value['subject']);
                }
            }

            $path = $this->controllerDir;
        }

        $count = count($tmp);


        for ($i = 0; $i < $count; $i++) {
            if (!is_dir($path.'/'.$tmp[$i])) {
                break;
            }
            $path .= '/'.$tmp[$i];
        }

        if (isset($tmp[$i])) {
            $this->controller = $tmp[$i];
            $i++;
            if (isset($tmp[$i])) {
                $this->action = $tmp[$i];
                $i++;
            }
        }

        $file = $path.'/'.$this->controller.'.php';

        $i && $tmp = array_slice($tmp, $i);
        if (!file_exists($file)) {
            throw new RouterException("Controller not exists: {$this->controller}: $file", 404);
        }

        $className = $this->controller . 'Controller';

        if (!class_exists($className, false)) {
            include $file;
        }

        $class = new $className($this);
        $actionName = $this->action . 'Action';

        try {
            $method = new ReflectionMethod($class, $actionName);
            if ($method->getNumberOfParameters() > 0) {
                $ps = array();
                foreach($method->getParameters() as $i => $val)
                {
                    $name = $val->getName();
                    $default = $val->isDefaultValueAvailable() ? $val->getDefaultValue() : '';
                    if (isset($tmp[$i])) {
                        $ps[] = self::getValue($tmp[$i], $default);
                    } elseif (isset($_GET[$name])) {
                        $ps[] = self::getValue($_GET[$name], $default);
                    } else {
                        $ps[] = $default;
                    }
                }
                return $method->invokeArgs($class, $ps);
            }
        } catch (ReflectionException $e) {

        } catch (Exception $e) {
            throw $e;
        }
        $doAction = array($class, $actionName);

        if (!is_callable($doAction, false)) {
            throw new RouterException("Action not exists: {$this->action}: $file", 500);
        }

        return call_user_func_array($doAction, $tmp);
    }
}


class RouterException extends Exception
{

}