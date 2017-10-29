<?php

namespace kapitanluffy\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use kapitanluffy\middleware\MiddlewareException;
use kapitanluffy\middleware\MiddlewareStackInterface;

class MiddlewareStack implements MiddlewareStackInterface
{
    protected $middlewares = array();

    protected $outerstack = null;

    public function __construct(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (is_array($middleware)) {
                $middleware = new self($middleware);
            }
            if ($middleware instanceof MiddlewareInterface) {
                $this->addMiddleware($middleware);
            }
            if ($middleware instanceof \Closure) {
                $this->addMiddlewareClosure($middleware);
            }
        }
    }

    protected function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    protected function addMiddlewareClosure(\Closure $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle(ServerRequestInterface $request)
    {
        if (empty($this->middlewares)) {
            throw new MiddlewareException("Middleware stack is empty.");
        }

        $middleware = current($this->middlewares);
        next($this->middlewares);

        if ($middleware == false && $this->outerstack) {
            return $this->outerstack->process($request, $this->outerstack);
        }

        if ($middleware == false) {
            throw new MiddlewareException("Middleware stack is exhausted. No middleware decided to return a response.");
        }

        if ($middleware instanceof \Closure) {
            $response = $middleware($request, $this);
        }
        else {
            $response = $middleware->process($request, $this);
        }

        if (!$response instanceof ResponseInterface) {
            throw new MiddlewareException("Middlewares should return a ResponseInterface");
        }

        return $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $stack)
    {
        if (!$this->outerstack && $this !== $stack) {
            $this->outerstack = $stack;
        }

        return $this->handle($request);
    }
}
