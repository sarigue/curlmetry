<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;

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
}
