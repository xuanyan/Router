[![Build Status](https://secure.travis-ci.org/xuanyan/Router.png?branch=master)](https://travis-ci.org/xuanyan/Router)

### How to use it

```
require_once __DIR__ . '/src/Router.php';

$router = new Router(__DIR__ . '/Controllers');

// set blog module
// handle url like: http://example.com/blog/controller/action, it was a rewrite url

$router->setModule('blog', __DIR__ . '/Blog/Controllers');

// run router
// handle url like: http://example.com/?url=controller/action
// sure, u can use rewrite to let the url seems better

$router->run(@$_GET['url']);

```