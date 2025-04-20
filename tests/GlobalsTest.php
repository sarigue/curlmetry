<?php

namespace Curlmetry\Test;

use Curlmetry\Globals;
use Curlmetry\GlobalTracer;
use Curlmetry\GlobalTracerProvider;
use Curlmetry\Tracer;
use Curlmetry\TracerProvider;
use PHPUnit\Framework\TestCase;

class GlobalsTest extends TestCase
{
    /**
     * Test that the setTracerProvider method sets the tracer provider correctly.
     */
    public function testSetTracerProvider()
    {
        $mockTracerProvider = $this->createMock(TracerProvider::class);

        Globals::setTracerProvider($mockTracerProvider);

        $this->assertSame($mockTracerProvider, GlobalTracerProvider::get());
    }

    /**
     * Test that the tracerProvider method returns the correct tracer provider after it has been set.
     */
    public function testTracerProviderReturnsCorrectProvider()
    {
        $mockTracerProvider = $this->createMock(TracerProvider::class);

        Globals::setTracerProvider($mockTracerProvider);

        $this->assertSame($mockTracerProvider, Globals::tracerProvider());
    }

    /**
     * Test that the tracerProvider method returns null if no tracer provider has been set.
     */
    public function testTracerProviderReturnsNullWhenNotSet()
    {
        Globals::setTracerProvider(null);
        $this->assertNull(Globals::tracerProvider());
    }

    /**
     * Test that the setTracer method sets the tracer correctly.
     */
    public function testSetTracer()
    {
        $mockTracer = $this->createMock(Tracer::class);

        Globals::setTracer($mockTracer);

        $this->assertSame($mockTracer, GlobalTracer::get());
    }

    /**
     * Test that the tracer method returns the correct tracer after it has been set.
     */
    public function testTracerReturnsCorrectTracer()
    {
        $mockTracer = $this->createMock(Tracer::class);

        Globals::setTracer($mockTracer);

        $this->assertSame($mockTracer, Globals::tracer());
        $this->assertNotNull(Globals::tracer(), 'Tracer should not be null when set.');
    }

    /**
     * Test that the tracer method returns null if no tracer has been set.
     */
    public function testTracerReturnsNullWhenNotSet()
    {
        Globals::setTracer(null);
        $this->assertNull(Globals::tracer());
    }
}
