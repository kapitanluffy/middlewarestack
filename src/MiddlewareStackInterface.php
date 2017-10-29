<?php

namespace kapitanluffy\middleware;

use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;

interface MiddlewareStackInterface extends MiddlewareInterface, RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request);

    public function process(ServerRequestInterface $request, RequestHandlerInterface $stack);
}
