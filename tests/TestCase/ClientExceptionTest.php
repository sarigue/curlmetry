<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\ClientException;
use Curlmetry\Test\CurlmetryTestCase;

class ClientExceptionTest extends CurlmetryTestCase
{
    public function testMessage()
    {
        $e = new ClientException("error");
        $this->assertEquals("error", $e->getMessage());
    }
}
