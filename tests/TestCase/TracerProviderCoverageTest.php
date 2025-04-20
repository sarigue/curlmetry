<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;

class TracerProviderCoverageTest extends CurlmetryTestCase
{
    public function testShutdown()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            )
        );
        $tracer = $provider->getTracer("demo");
        $span = $tracer->startSpan("shutdown.test");
        $span->attach();
        $provider->shutdown();

        $this->assertTrue($span->isEnded());
    }

    public function testGetTracerWithSchema()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            )
        );
        $tracer = $provider->getTracer("curlmetry", "1.0.0", "https://opentelemetry.io/schemas/1.13.0");

        $this->assertEquals("curlmetry", $tracer->getName());
        $this->assertEquals("1.0.0", $tracer->getVersion());
        $this->assertEquals("https://opentelemetry.io/schemas/1.13.0", $tracer->getSchemaUrl());
    }
}
