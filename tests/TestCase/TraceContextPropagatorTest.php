<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Propagation\TraceContextPropagator;
use Curlmetry\Span;
use Curlmetry\Test\CurlmetryTestCase;

class TraceContextPropagatorTest extends CurlmetryTestCase
{
    public function testInjectExtract()
    {
        $span = new Span('propagation.test', 'abcd1234abcd1234abcd1234abcd1234');
        $span->spanId = 'ef12a90bac64ee37';
        $headers = [];
        TraceContextPropagator::inject($span, $headers);

        $this->assertArrayHasKey('traceparent', $headers);

        $extracted = TraceContextPropagator::extract(['TraceParent' => $headers['traceparent']]);
        $this->assertEquals('abcd1234abcd1234abcd1234abcd1234', $extracted['traceId']);
        $this->assertEquals('ef12a90bac64ee37', $extracted['spanId']);
    }
}
