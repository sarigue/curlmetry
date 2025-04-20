<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\GlobalTracer;
use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\Test\CurlmetryTestCase;

class GlobalTracerTest extends CurlmetryTestCase
{
    public function testSetAndGet()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler()
        );
        GlobalTracer::set($tracer);

        $this->assertSame($tracer, GlobalTracer::get());
    }
}
