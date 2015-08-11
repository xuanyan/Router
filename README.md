[![Build Status](https://secure.travis-ci.org/xuanyan/Router.png?branch=master)](https://travis-ci.org/xuanyan/Router)

### How to use it

```php

require_once __DIR__ . '/src/Router.php';

use XuanYan\Router as Router;

$router = new Router(__DIR__ . '/Controllers');

// set blog module
// handle url like: http://example.com/blog/controller/action, it was a rewrite url

$router->setModule('blog', __DIR__ . '/Blog/Controllers');


// map blog/xuanyan  to blogdb/xuanyan
$router->map('blog/:username', 'blogdb/:username');
// if u just want to handler the number you could use :NUM
$router->map('user/:NUM/profile', 'user/profile/:NUM');


// run router
// handle url like: http://example.com/?url=controller/action
// sure, u can use rewrite to let the url seems better
$router->run(@$_GET['url']);

```