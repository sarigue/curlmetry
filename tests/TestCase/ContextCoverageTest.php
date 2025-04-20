<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Span;
use Curlmetry\Test\CurlmetryTestCase;

class ContextCoverageTest extends CurlmetryTestCase
{
    public function testContextClear()
    {
        $span = new Span("ctx.test", "trace", null);
        Context::push($span);
        $this->assertSame($span, Context::current());

        Context::clear();
        $this->assertNull(Context::current());
    }
}
