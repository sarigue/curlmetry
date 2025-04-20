<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;

class TracerProviderTest extends CurlmetryTestCase
{
    public function testGetTracerWithDefaults()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor((new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'), 'default')
        );
        $tracer = $provider->getTracer();

        $this->assertNotEmpty($tracer->getTraceId());
        $this->assertEquals('default', $tracer->getName());
    }
}
