<?php

namespace Curlmetry;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Curlmetry\Propagation\TraceContextPropagator;

/**
 * Middleware class that implements the MiddlewareInterface to process HTTP requests and responses.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Middleware implements MiddlewareInterface
{
    private $tracer;

    public function __construct(Tracer $tracer)
    {
        $this->tracer = $tracer;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws Exception\ContextException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        // Extract traceparent context if exists
        $traceContext = TraceContextPropagator::extract($request->getHeaders());

        if ($traceContext) {
            Context::setTraceId($traceContext['traceId']);
        }

        $span = $this->tracer->spanBuilder($request->getMethod() . ' ' . (string) $request->getUri())
            ->startSpan();
        $scope = $span->attach();

        try {
            $response = $handler->handle($request);
            $span->setStatus('OK');
            return $response;
        } catch (\Exception $e) {
            $span->recordException($e);
            $span->setStatus('ERROR', $e->getMessage());
            throw $e;
        } finally {
            $scope->detach();
            $this->tracer->endSpan($span);
        }
    }
}
