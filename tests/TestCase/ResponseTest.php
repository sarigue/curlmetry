<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\Response;
use Curlmetry\Test\CurlmetryTestCase;

class ResponseTest extends CurlmetryTestCase
{
    public function testResponseConstruction()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers, '{"ok":true}', "OK");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getReasonPhrase());
        $this->assertEquals('{"ok":true}', (string) $response->getBody());
    }

    /**
     * Test that withAddedHeader adds a new header if it does not already exist.
     */
    public function testWithAddedHeaderAddsNewHeader()
    {
        $response = new Response();
        $newResponse = $response->withAddedHeader('Custom-Header', 'value');

        $this->assertFalse($response->hasHeader('Custom-Header'));
        $this->assertTrue($newResponse->hasHeader('Custom-Header'));
        $this->assertEquals(['value'], $newResponse->getHeader('Custom-Header'));
    }

    /**
     * Test that withAddedHeader appends values to an existing header.
     */
    public function testWithAddedHeaderAppendsValueToExistingHeader()
    {
        $headers = ["Custom-Header" => ["value1"]];
        $response = new Response(200, $headers);
        $newResponse = $response->withAddedHeader('Custom-Header', 'value2');

        $this->assertEquals(['value1'], $response->getHeader('Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $newResponse->getHeader('Custom-Header'));
    }

    /**
     * Test that withAddedHeader treats header names in a case-insensitive way.
     */
    public function testWithAddedHeaderIsCaseInsensitive()
    {
        $response = new Response();
        $newResponse = $response->withAddedHeader('Custom-Header', 'value1');
        $updatedResponse = $newResponse->withAddedHeader('custom-header', 'value2');

        $this->assertEquals(['value1'], $newResponse->getHeader('Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $updatedResponse->getHeader('Custom-Header'));
    }

    /**
     * Test that withHeader adds or updates a header in a case-insensitive manner.
     */
    public function testWithHeaderAddsOrUpdatesHeader()
    {
        $response = new Response();
        $newResponse = $response->withHeader('Content-Type', 'application/json');

        $this->assertFalse($response->hasHeader('Content-Type'));
        $this->assertTrue($newResponse->hasHeader('Content-Type'));
        $this->assertEquals(['application/json'], $newResponse->getHeader('Content-Type'));
    }

    /**
     * Test that withHeader treats header names in a case-insensitive way.
     */
    public function testWithHeaderIsCaseInsensitive()
    {
        $response = new Response();
        $newResponse = $response->withHeader('Content-Type', 'application/json');
        $anotherResponse = $newResponse->withHeader('content-type', 'text/html');

        $this->assertEquals(['application/json'], $newResponse->getHeader('Content-Type'));
        $this->assertEquals(['text/html'], $anotherResponse->getHeader('Content-Type'));
    }

    /**
     * Test cloning behavior of withHeader.
     */
    public function testWithHeaderCloningBehavior()
    {
        $response = new Response();
        $newResponse1 = $response->withHeader('Content-Type', 'application/json');
        $newResponse2 = $newResponse1->withHeader('Authorization', 'Bearer token');

        $this->assertFalse($response->hasHeader('Content-Type'));
        $this->assertFalse($response->hasHeader('Authorization'));
        $this->assertTrue($newResponse1->hasHeader('Content-Type'));
        $this->assertFalse($newResponse1->hasHeader('Authorization'));
        $this->assertTrue($newResponse2->hasHeader('Content-Type'));
        $this->assertTrue($newResponse2->hasHeader('Authorization'));
    }

    public function testWithProtocolVersionUpdatesProtocolVersion()
    {
        $response = new Response();

        $newResponse = $response->withProtocolVersion('2.0');

        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals('2.0', $newResponse->getProtocolVersion());
    }

    public function testWithProtocolVersionCloningBehavior()
    {
        $response = new Response();

        $newResponse1 = $response->withProtocolVersion('2.0');
        $newResponse2 = $newResponse1->withProtocolVersion('3.0');

        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals('2.0', $newResponse1->getProtocolVersion());
        $this->assertEquals('3.0', $newResponse2->getProtocolVersion());
    }

    /**
     * Test that withBody updates the body of the response.
     */
    public function testWithBodyUpdatesBody()
    {
        $response = new Response();

        $newBody = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $newResponse = $response->withBody($newBody);

        $this->assertNotSame($response->getBody(), $newResponse->getBody());
        $this->assertSame($newBody, $newResponse->getBody());
    }

    /**
     * Test that withBody cloning behavior does not modify the original response.
     */
    public function testWithBodyCloningBehavior()
    {
        $originalBody = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $newBody = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $response = new Response(200, [], $originalBody);
        $newResponse = $response->withBody($newBody);

        $this->assertSame($originalBody, $response->getBody());
        $this->assertSame($newBody, $newResponse->getBody());
    }

    /**
     * Test that hasHeader returns true when a header exists.
     */
    public function testHasHeaderReturnsTrueIfHeaderExists()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers, '{"ok":true}');

        $this->assertTrue($response->hasHeader("Content-Type"));
    }

    /**
     * Test that withoutHeader removes an existing header.
     */
    public function testWithoutHeaderRemovesExistingHeader()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers);

        $newResponse = $response->withoutHeader("Content-Type");

        $this->assertTrue($response->hasHeader("Content-Type"));
        $this->assertFalse($newResponse->hasHeader("Content-Type"));
        $this->assertEquals([], $newResponse->getHeader("Content-Type"));
    }

    /**
     * Test that withoutHeader treats header names in a case-insensitive way.
     */
    public function testWithoutHeaderIsCaseInsensitive()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers);

        $newResponse = $response->withoutHeader("content-type");

        $this->assertTrue($response->hasHeader("Content-Type"));
        $this->assertFalse($newResponse->hasHeader("Content-Type"));
        $this->assertEquals([], $newResponse->getHeader("CONTENT-TYPE"));
    }

    /**
     * Test that withoutHeader does not affect the original response.
     */
    public function testWithoutHeaderDoesNotAffectOriginalResponse()
    {
        $headers = ["Content-Type" => ["application/json"], "Authorization" => ["Bearer token"]];
        $response = new Response(200, $headers);

        $newResponse = $response->withoutHeader("Authorization");

        $this->assertTrue($response->hasHeader("Authorization"));
        $this->assertFalse($newResponse->hasHeader("Authorization"));
        $this->assertEquals(["Bearer token"], $response->getHeader("Authorization"));
        $this->assertEquals([], $newResponse->getHeader("Authorization"));
    }

    /**
     * Test that hasHeader returns false when a header does not exist.
     */
    public function testHasHeaderReturnsFalseIfHeaderDoesNotExist()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers, '{"ok":true}');

        $this->assertFalse($response->hasHeader("Authorization"));
    }

    /**
     * Test that hasHeader is case-insensitive.
     */
    public function testHasHeaderIsCaseInsensitive()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers, '{"ok":true}');

        $this->assertTrue($response->hasHeader("content-type"));
        $this->assertTrue($response->hasHeader("CONTENT-TYPE"));
    }

    /**
     * Test that withStatus updates the status code and reason phrase.
     */
    public function testWithStatusUpdatesStatusCodeAndReasonPhrase()
    {
        $response = new Response(200, [], '', 'OK');

        $newResponse = $response->withStatus(404, 'Not Found');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals(404, $newResponse->getStatusCode());
        $this->assertEquals('Not Found', $newResponse->getReasonPhrase());
    }

    /**
     * Test cloning behavior of withStatus.
     */
    public function testWithStatusCloningBehavior()
    {
        $response = new Response(200, [], '', 'OK');

        $newResponse1 = $response->withStatus(404, 'Not Found');
        $newResponse2 = $newResponse1->withStatus(500, 'Internal Server Error');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals(404, $newResponse1->getStatusCode());
        $this->assertEquals('Not Found', $newResponse1->getReasonPhrase());
        $this->assertEquals(500, $newResponse2->getStatusCode());
        $this->assertEquals('Internal Server Error', $newResponse2->getReasonPhrase());
    }

    /**
     * Test that getHeaders returns all headers correctly.
     */
    public function testGetHeadersReturnsAllHeaders()
    {
        $headers = [
            "Content-Type" => ["application/json"],
            "Authorization" => ["Bearer token"]
        ];
        $response = new Response(200, $headers, '{"ok":true}');

        $this->assertEquals(
            [
                "content-type" => ["application/json"],
                "authorization" => ["Bearer token"]
            ],
            $response->getHeaders()
        );
    }

    /**
     * Test that getHeaders returns an empty array when no headers are set.
     */
    public function testGetHeadersReturnsEmptyArrayIfNoHeaders()
    {
        $response = new Response();

        $this->assertEquals([], $response->getHeaders());
    }

    /**
     * Test that getHeaderLine returns the correct string for a single header value.
     */
    public function testGetHeaderLineReturnsSingleHeaderValue()
    {
        $headers = ["Content-Type" => ["application/json"]];
        $response = new Response(200, $headers);

        $this->assertEquals("application/json", $response->getHeaderLine("Content-Type"));
    }

    /**
     * Test that getHeaderLine returns concatenated string for multiple header values.
     */
    public function testGetHeaderLineReturnsMultipleHeaderValuesConcatenated()
    {
        $headers = ["Set-Cookie" => ["cookie1=value1", "cookie2=value2"]];
        $response = new Response(200, $headers);

        $this->assertEquals("cookie1=value1, cookie2=value2", $response->getHeaderLine("Set-Cookie"));
    }

    /**
     * Test that getHeaderLine returns an empty string when the header does not exist.
     */
    public function testGetHeaderLineReturnsEmptyStringIfHeaderDoesNotExist()
    {
        $response = new Response();

        $this->assertEquals("", $response->getHeaderLine("Non-Existent-Header"));
    }

    /**
     * Test that getHeaderLine treats header names in a case-insensitive manner.
     */
    public function testGetHeaderLineIsCaseInsensitive()
    {
        $headers = ["Authorization" => ["Bearer token"]];
        $response = new Response(200, $headers);

        $this->assertEquals("Bearer token", $response->getHeaderLine("authorization"));
        $this->assertEquals("Bearer token", $response->getHeaderLine("AUTHORIZATION"));
    }
}
