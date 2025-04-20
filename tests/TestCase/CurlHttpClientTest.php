<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\ClientException;
use Curlmetry\Psr\CurlHttpClient;
use Curlmetry\Psr\Request;
use Curlmetry\Psr\StringStream;
use Curlmetry\Test\CurlmetryTestCase;

class CurlHttpClientTest extends CurlmetryTestCase
{
    /**
     * @throws ClientException
     */
    public function testSimplePostRequest()
    {
        $client = new CurlHttpClient();
        $request = new Request('POST', 'https://httpbin.org/post');
        $request = $request->withHeader('Content-Type', 'application/json')
                           ->withBody(new StringStream(json_encode(['foo' => 'bar'])));

        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }
}
