<?php

namespace Curlmetry\Test;

use Curlmetry\GlobalTracerProvider;
use Curlmetry\TracerProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test class for GlobalTracerProvider
 */
class GlobalTracerProviderTest extends TestCase
{
    /**
     * Test case: get method returns null when no provider is set
     */
    public function testGetReturnsNullWhenNoProviderIsSet()
    {
        $this->assertNull(GlobalTracerProvider::get());
    }

    /**
     * Test case: get method returns the provider set by the set method
     */
    public function testGetReturnsProviderSetBySetMethod()
    {
        $mockProvider = $this->createMock(TracerProvider::class);

        GlobalTracerProvider::set($mockProvider);
        $this->assertSame($mockProvider, GlobalTracerProvider::get());
    }
}
