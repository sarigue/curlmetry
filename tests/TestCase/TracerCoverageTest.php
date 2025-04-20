<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Scope;
use Curlmetry\Span;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\Test\CurlmetryTestCase;

class TracerCoverageTest extends CurlmetryTestCase
{
    public function testStartAndEndSpan()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            'unit-tracer'
        );
        $span = $tracer->startSpan('core.logic');
        $scope = $span->attach();
        $this->assertEquals($scope, Scope::current());
        $this->assertInstanceOf(Span::class, $span);
        $tracer->endSpan($span);
        $this->assertNotEquals($scope, Scope::current());
        $this->assertNotEquals($span, Context::current());
        $this->assertTrue($span->isEnded());
    }

    public function testStartActiveSpan()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            'unit-tracer'
        );

        $mock = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['callbackMethod'])
            ->getMock();

        $mock->expects($this->once())
            ->method('callbackMethod')
            ->with($this->callback(function ($span) {
 /** @var Span $span */
                $this->assertNotNull($span, 'Callback argument is null.');
                $this->assertInstanceOf(Span::class, $span, 'Argument is not a Span');
                $span->setAttribute('inside', true);
                return true;
            }));

        $span = $tracer->startActiveSpan('active.logic', [$mock, 'callbackMethod']);
        $this->assertEquals('active.logic', $span->name);
        $this->assertArrayHasKey('inside', $span->attributes);
        $this->assertTrue($span->isEnded());
    }

    public function testExceptionOnStartActiveSpan()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(456);
        $this->expectExceptionMessage('exception.on.span');

        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            'unit-tracer'
        );

        $tracer->startActiveSpan('active.logic', function ($span) {
            throw new \Exception('exception.on.span', 456);
        });
    }

    public function testWithTraceId()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler()
        );
        $customId = "abcd1234abcd1234abcd1234abcd1234";
        $tracer = $tracer->withTraceId($customId);
        $this->assertEquals($customId, $tracer->getTraceId());
    }
}
