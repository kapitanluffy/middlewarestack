# Middleware Stack

Implementing PSR-15 Middlewares in a single class.

###### How to Use

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
