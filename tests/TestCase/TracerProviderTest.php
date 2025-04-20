<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\CurlmetryTestCase;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;

class TracerProviderTest extends CurlmetryTestCase
{
    public function testGetTracerWithDefaults()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'default'
            )
        );
        $tracer = $provider->getTracer();

        $this->assertNotEmpty($tracer->getTraceId());
        $this->assertEquals('default', $tracer->getName());
    }

    public function testGetGlobalReturnsCorrectInstance()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'global_instance'
            )
        );

        // Use the `setAsGlobal` method to set the global instance
        $provider->setAsGlobal();

        // Retrieve the global instance and verify it is the set provider
        $globalProvider = TracerProvider::getGlobal();

        $this->assertSame($provider, $globalProvider);
    }
}
