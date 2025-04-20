<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Context;
use Curlmetry\Span;
use Curlmetry\Test\CurlmetryTestCase;

class ScopeFullTest extends CurlmetryTestCase
{
    public function testDetachIsIdempotent()
    {
        $span = new Span('scope.detach', 'trace', null);
        $scope = $span->attach();

        $this->assertSame($span, Context::current());
        $scope->detach();
        $this->assertNull(Context::current());

        // Call detach again — should not throw
        $scope->detach();
        $this->assertNull(Context::current());
    }

    public function testStringify()
    {
        $span = new Span('scope.detach', 'trace', null);
        $scope = $span->attach();

        $this->assertSame($span, Context::current());
        $str = (string)$scope;
        $this->assertNotEmpty($str);
        $this->assertStringContainsString('"span":', $str, 'Span should be in stringified scope');
        ;
    }
}
