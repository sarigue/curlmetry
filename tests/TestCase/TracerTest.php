<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\GlobalTracer;
use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\CurlmetryTestCase;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\TracerProvider;

class TracerTest extends CurlmetryTestCase
{
    public function testStartSpan()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor((new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'), 'servicename')
        );
        $tracer = $provider->getTracer('test');

        $span = $tracer->startSpan('unit.test');
        $this->assertInstanceOf('Curlmetry\\Span', $span);
    }

    public function testTracerGetGlobalReturnsSameInstance()
    {
        $processor = new SimpleSpanProcessor(
            (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
            'service.name'
        );
        $sampler = new AlwaysOnSampler();
        $tracer = new Tracer($processor, $sampler);
        $tracer->setAsGlobal();

        $retrievedTracer = Tracer::getGlobal();
        $this->assertSame($tracer, $retrievedTracer);
    }

    public function testTracerGetGlobalReturnsNullIfNotSet()
    {
        GlobalTracer::set(null);
        $retrievedTracer = Tracer::getGlobal();
        $this->assertNull($retrievedTracer);
    }
}
