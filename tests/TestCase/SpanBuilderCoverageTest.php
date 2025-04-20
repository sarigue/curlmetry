<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\SpanBuilder;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\Test\CurlmetryTestCase;

class SpanBuilderCoverageTest extends CurlmetryTestCase
{
    public function testNoParentSpan()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            "no-parent"
        );
        $builder = new SpanBuilder("spanBuilder.test", $tracer);
        $builder->setNoParent();
        $span = $builder->startSpan();

        $this->assertNull($span->parentSpanId);
    }

    public function testWithStartTime()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            "timing"
        );

        $builder = new SpanBuilder('timed.span', $tracer);
        $builder->setStartTimestamp(123456789);
        $this->assertEquals($builder, $builder->setSpanKind('CLIENT'));
        $this->assertEquals('CLIENT', $builder->getKind());

        $span = $builder->startSpan();
        $this->assertEquals(123456789, $span->startTime);
    }

    public function testJson()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            "timing"
        );
        $builder = new SpanBuilder('timed.span', $tracer);
        $builder->setStartTimestamp(123456789);

        $json = json_encode($builder);
        $this->assertStringContainsString('"startTime":123456789', $json);
    }

    public function testFromJson()
    {
        $json = '{
            "name":"timed.span",
            "tracer":{
                "traceId":"420b83711634e60ad9990bd278a9c9b2",
                "processor":{
                    "serviceName":"service.name",
                    "exporter":{
                        "endpoint":"http:\/\/localhost"
                    },
                    "exporterClass":"Curlmetry\\\\Exporter\\\\OtlpExporter"
                },
                "processorClass":"Curlmetry\\\\Processor\\\\SimpleSpanProcessor",
                "sampler":[],
                "samplerClass":"Curlmetry\\\\Sampling\\\\AlwaysOnSampler",
                "name":"timing",
                "version":null,
                "schemaUrl":null
            },
            "tracerClass":"Curlmetry\\\\Tracer",
            "explicitParent":null,
            "noParent":false,
            "attributes":[],
            "startTime":123456789,
            "kind":"INTERNAL"
        }';

        $builder = SpanBuilder::fromJson($json);

        $this->assertInstanceOf(SpanBuilder::class, $builder);
        $this->assertEquals('timed.span', $builder->getName());
        $this->assertEquals('INTERNAL', $builder->getKind());
        $this->assertEquals('123456789', $builder->getStartTime());
        $this->assertEquals('420b83711634e60ad9990bd278a9c9b2', $builder->getTracer()->getTraceId());
    }

    public function testStringify()
    {
        $tracer = new Tracer(
            new SimpleSpanProcessor(
                (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'),
                'service.name'
            ),
            new AlwaysOnSampler(),
            "timing"
        );
        $builder = new SpanBuilder('timed.span', $tracer);
        $this->assertEquals(json_encode($builder, JSON_PRETTY_PRINT), (string)$builder);
    }
}
