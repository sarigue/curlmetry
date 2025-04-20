<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Middleware;
use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;
use Psr\Http\Message\ResponseInterface;

class MiddlewareTest extends CurlmetryTestCase
{
    public function testMiddlewareCreatesSpan()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            )
        );
        $middleware = new Middleware($provider->getTracer());

        $request = new \Curlmetry\Test\Tools\ServerRequest('GET', 'https://example.com');
        $handler = new \Curlmetry\Test\Tools\ServerHandler();

        $response = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMiddlewareHandlesException()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            )
        );
        $middleware = new Middleware($provider->getTracer());

        $request = new \Curlmetry\Test\Tools\ServerRequest('POST', 'https://example.com/invalid');
        $handler = $this->createMock(\Psr\Http\Server\RequestHandlerInterface::class);

        $handler
            ->method('handle')
            ->willThrowException(new \RuntimeException('Test Exception'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test Exception');

        try {
            $middleware->process($request, $handler);
        } catch (\RuntimeException $e) {
            $this->assertTrue(true, 'Exception properly caught.');
            throw $e;
        }
    }

    public function testMiddlewareReadsTraceContextFromHeaders()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            )
        );
        $middleware = new Middleware($provider->getTracer());

        $request = new \Curlmetry\Test\Tools\ServerRequest('GET', 'https://example.com', [
            'traceparent' => ['00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01']
        ]);
        $handler = new \Curlmetry\Test\Tools\ServerHandler();

        $middleware->process($request, $handler);

        $this->assertEquals('4bf92f3577b34da6a3ce929d0e0e4736', Context::getTraceId());
    }
}
