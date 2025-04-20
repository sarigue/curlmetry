<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Span;
use Curlmetry\Test\CurlmetryTestCase;

class ContextSetTraceIdTest extends CurlmetryTestCase
{
    public function testSetTraceId()
    {
        $span = new Span('traceid.test', 'traceid123', null);
        Context::push($span);
        Context::setTraceId('override123');

        $this->assertEquals('override123', Context::current()->traceId);
    }
}
