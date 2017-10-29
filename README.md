# Middleware Stack

Implementing [PSR-15](https://github.com/php-fig/fig-standards/tree/master/proposed/http-middleware) Middlewares in a single class.

###### Why?

As defined in the proposal, middlewares "processes" a request and returns a response. Similarly, RequestHandlers "handles" a request and returns a response.
The idea of this package is to simplify the goals of the interfaces provided by PSR-15.

###### Install

```
composer require kapitanluffy/middlewarestack
```

###### Usage

```php
use kapitanluffy\middleware\MiddlewareStack;
use Zend\Diactoros\ServerRequestFactory;

$innerStack = new MiddlewareStack(array(
    new Middlewares\Robots(false),
    new Middlewares\TrailingSlash(false)
));

$stack = new MiddlewareStack(array(
    new Middlewares\Whoops(),

    // Use another stack as middleware
    $innerStack,

    // Create an inner stack with arrays
    array(
        new Middlewares\ResponseTime(),
        new Middlewares\Uuid(),
    ),

    // Create closures as middlewares
    function ($request, $stack) {
        $response = $stack->handle($request);
        return $response->withHeader('X-Passed-MiddlewareClosure', 1);
    }
));

// let the middleware stack handle the request
$request = ServerRequestFactory::fromGlobals();
$response = $stack->handle($request);
```
