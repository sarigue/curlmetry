<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Span;
use Curlmetry\SpanBuilder;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\Test\CurlmetryTestCase;

class SpanBuilderTest extends CurlmetryTestCase
{
    public function testStartSpan()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'test'
            ),
            new AlwaysOnSampler(),
            'test'
        );
        $builder = new SpanBuilder('my.op', $tracer);
        $builder->setAttribute('my.attribute', 'my.attribute.value');
        $span = $builder->startSpan();

        $this->assertInstanceOf(Span::class, $span);
        $this->assertEquals('my.op', $span->name);
        $this->assertArrayHasKey('my.attribute', $span->attributes);
        $this->assertEquals('my.attribute.value', $span->attributes['my.attribute']);
    }

    public function testParenting()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor((new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'), 'test'),
            new AlwaysOnSampler(),
            'test'
        );
        $parent = $tracer->startSpan('parent');
        $builder = new SpanBuilder('child', $tracer);
        $builder->setParent($parent);
        $span = $builder->startSpan();

        $this->assertEquals($parent->spanId, $span->parentSpanId);
    }
}
