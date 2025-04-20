<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Processor\BatchSpanProcessor;
use Curlmetry\Span;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Test\CurlmetryTestCase;

class BatchSpanProcessorTest extends CurlmetryTestCase
{
    public function testBatchingAndFlush()
    {
        $exporter = (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null');

        $mockProcessor = $this->getMockBuilder(BatchSpanProcessor::class)
            ->onlyMethods(['flush'])
            ->setConstructorArgs([$exporter, 'batch-service', 2])
            ->getMock();

        $mockProcessor->expects($this->once())
            ->method('flush')
        ;

        $span1 = new Span("batch.one", "traceX", 'parent1234567890');
        $span1->end();
        $mockProcessor->onEnd($span1); // Stack

        $span2 = new Span("batch.two", "traceX", 'parent1234567890');
        $span2->end();
        $mockProcessor->onEnd($span2); // Should trigger flush
    }

    public function testImmediateFlushOnEnd()
    {
        $exporter = (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null');

        $mockProcessor = $this->getMockBuilder(BatchSpanProcessor::class)
            ->onlyMethods(['flush'])
            ->setConstructorArgs([$exporter, 'batch-service', 2])
            ->getMock();

        $mockProcessor->expects($this->exactly(2))->method('flush');

        $span1 = new Span("batch.one", "traceX", null);
        $span1->end();
        $mockProcessor->onEnd($span1); // Should trigger flush

        $span2 = new Span("batch.two", "traceX", null);
        $span2->end();
        $mockProcessor->onEnd($span2); // Should trigger flush
    }

    public function testFlushOnShutdown()
    {
        $exporter = (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null');

        $mockProcessor = $this->getMockBuilder(BatchSpanProcessor::class)
            ->onlyMethods(['flush'])
            ->setConstructorArgs([$exporter, 'batch-service', 20])
            ->getMock();

        $mockProcessor->expects($this->once())->method('flush');

        $span1 = new Span("batch.one", "traceX", 'parent1234567890');
        $span1->end();
        $mockProcessor->onEnd($span1); // Stack

        $span2 = new Span("batch.two", "traceX", 'parent1234567890');
        $span2->end();
        $mockProcessor->onEnd($span2); // Stack

        $mockProcessor->shutdown(); // flush
    }

    public function testFlushCallExport()
    {
        $mockExporter = $this->getMockBuilder(OtlpDebugExporter::class)
            ->onlyMethods(['export'])
            ->setConstructorArgs(['http://localhost'])
            ->getMock();

        $processor = new BatchSpanProcessor($mockExporter, 'batch-service', 20);

        $mockExporter->expects($this->once())->method('export');

        $span1 = new Span("batch.one", "traceX", null);
        $span1->end();
        $processor->onEnd($span1);
    }

    public function testToJson()
    {
        $exporter = (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null');
        $processor = new BatchSpanProcessor($exporter, 'batch-service', 2);
        $json = json_encode($processor);
        $this->assertStringContainsCompat('"exporter":', $json);
        $this->assertStringContainsCompat('"exporterClass":', $json);
        $this->assertStringContainsCompat('"serviceName":"batch-service"', $json);
        $this->assertStringContainsCompat('"batch":[]', $json);
        $this->assertStringContainsCompat('"maxBatchSize":2', $json);
    }

    public function testFromJson()
    {
        $processor = BatchSpanProcessor::fromJson('{
            "exporter": {"endpoint": "http://localhost"},
            "exporterClass":"Curlmetry\\\\Test\\\\Tools\\\\OtlpDebugExporter",
            "serviceName":"batch-service",
            "batch":[],
            "maxBatchSize":20
        }');

        $this->assertInstanceOf(BatchSpanProcessor::class, $processor);
    }
}
