<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;

class TracerInfoTest extends CurlmetryTestCase
{
    public function testTracerMetaData()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor((new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'), 'default')
        );
        $tracer = $provider->getTracer('curlmetry', '1.0.0', 'https://opentelemetry.io/schemas/1.13.0');

        $this->assertInstanceOf(AlwaysOnSampler::class, $tracer->getSampler());
        $this->assertEquals('curlmetry', $tracer->getName());
        $this->assertEquals('1.0.0', $tracer->getVersion());
        $this->assertEquals('https://opentelemetry.io/schemas/1.13.0', $tracer->getSchemaUrl());
    }
}
