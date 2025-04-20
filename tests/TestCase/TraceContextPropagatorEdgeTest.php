<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Propagation\TraceContextPropagator;
use Curlmetry\Span;
use Curlmetry\Test\CurlmetryTestCase;

class TraceContextPropagatorEdgeTest extends CurlmetryTestCase
{
    public function testExtractWithInvalidFormat()
    {
        $res = TraceContextPropagator::extract(['traceparent' => 'malformed']);
        $this->assertNull($res);
    }

    public function testInjectCreatesHeader()
    {
        $span = new Span('inject', 'trace123456789012345678901234567', 'parent1234567890');
        $headers = [];
        $success = TraceContextPropagator::inject($span, $headers);

        $this->assertTrue($success);
        $this->assertArrayHasKey('traceparent', $headers);
        $this->assertStringContainsString('trace123', $headers['traceparent']);
    }

    public function testErrorOnInjectCreatesHeader1()
    {
        $span = new Span('inject', 'trace123456789012345678901234567890', 'parent1234567890');
        $headers = [];
        $success = TraceContextPropagator::inject($span, $headers);

        $this->assertFalse($success);
        $this->assertArrayNotHasKey('traceparent', $headers);
    }
}
