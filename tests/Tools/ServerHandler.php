<?php

namespace Curlmetry\Test\Tools;

class ServerHandler implements \Psr\Http\Server\RequestHandlerInterface
{
    public function handle(\Psr\Http\Message\ServerRequestInterface $request)
    {
        return new ServerResponse(200, [], 'OK', 'OK');
    }
}
