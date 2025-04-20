<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\CurlHttpClient;
use Curlmetry\Psr\Request;
use Curlmetry\Test\CurlmetryTestCase;

class CurlHttpClientFunctionalTest extends CurlmetryTestCase
{
    public function testSendRequest()
    {
        $client = new CurlHttpClient();
        $request = new Request("GET", "https://httpbin.org/get");
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }
}
