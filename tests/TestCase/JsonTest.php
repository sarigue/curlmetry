<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Exporter\OtlpExporter;
use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Scope;
use Curlmetry\Span;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;
use Curlmetry\TracerProvider;
use Curlmetry\Test\CurlmetryTestCase;

class JsonTest extends CurlmetryTestCase
{
    public function testExportSpan()
    {
        $provider = new TracerProvider(
            new AlwaysOnSampler(),
            new SimpleSpanProcessor((new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null'), 'servicename')
        );
        $tracer = $provider->getTracer('test');
        $span = $tracer->startSpan('unit.test');

        $json_provider = json_encode($provider);
        $json_tracer   = json_encode($tracer);
        $json_span     = json_encode($span);

        $scope = $span->activate();
        $json_context = Context::saveToJson();
        $json_scope   = json_encode($scope);
        $scope->close();

        $this->assertArrayHasKey('sampler', json_decode($json_provider, true));
        $this->assertArrayHasKey('processor', json_decode($json_provider, true));
        $this->assertArrayHasKey('traceId', json_decode($json_tracer, true));
        $this->assertArrayHasKey('spanId', json_decode($json_span, true));
        $this->assertArrayHasKey('stack', json_decode($json_context, true));
        $this->assertArrayHasKey('span', json_decode($json_scope, true));
    }

    public function testImportSpan()
    {
        $span     = Span::fromJson('{"spanId":"my.span.id"}');
        $tracer   = Tracer::fromJson('{
            "traceId":"my.trace.id",
            "sampler":[],
            "samplerClass":"Curlmetry\\\\Sampling\\\\AlwaysonSampler",
            "processor":{
                "exporter":{
                    "endpoint":"http://localhost.tld"
                },
                "exporterClass":"Curlmetry\\\\Exporter\\\\OtlpExporter",
                "serviceName":"servicename"
            },
            "processorClass":"Curlmetry\\\\Processor\\\\SimpleSpanProcessor"
        }');
        $provider = TracerProvider::fromJson('{
            "sampler":[],
            "samplerClass":"Curlmetry\\\\Sampling\\\\AlwaysonSampler",
            "processor":{
                "exporter":{
                    "endpoint":"http://localhost.tld"
                },
                "exporterClass":"Curlmetry\\\\Exporter\\\\OtlpExporter",
                "serviceName":"servicename"
            },
            "processorClass":"Curlmetry\\\\Processor\\\\SimpleSpanProcessor"
        }');

        Context::restoreFromJson('{"stack":[{"spanId":"span1"},{"spanId":"span2"}]}');
        Scope::fromJson('{"span":{"spanId":"scoped.span.id"}}');

        $processorReflect = new \ReflectionClass(SimpleSpanProcessor::class);
        $processorExporter = $processorReflect->getProperty('exporter');
        $processorExporter->setAccessible(true);

        $this->assertEquals('my.trace.id', $tracer->getTraceId());
        $this->assertEquals('my.span.id', $span->spanId);
        $this->assertInstanceOf(AlwaysonSampler::class, $provider->getSampler());
        $this->assertInstanceOf(SimpleSpanProcessor::class, $provider->getProcessor());

        $processor = $provider->getProcessor();
        $exporter  = $processorExporter->getValue($processor); /** @var OtlpExporter $exporter */

        $this->assertInstanceOf(OtlpExporter::class, $exporter);

        $this->assertEquals('http://localhost.tld', $exporter->getEndpoint());
        Context::clear();
        ;
    }
}
