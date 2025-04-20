<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\Request;
use Curlmetry\Psr\StringStream;
use Curlmetry\Test\CurlmetryTestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends CurlmetryTestCase
{
    /**
     * Tests that withBody updates the body of the request and creates a new instance.
     */
    public function testWithBodyUpdatesBodyAndCreatesNewInstance()
    {
        $request = new Request('POST', 'https://example.com');
        $newBody = new StringStream('{"key":"value"}');
        $newRequest = $request->withBody($newBody);

        $this->assertNotSame($request, $newRequest);
        $this->assertEmpty((string)$request->getBody());
        $this->assertEquals('{"key":"value"}', (string)$newRequest->getBody());
    }

    /**
     * Tests that withBody does not modify the original request instance.
     */
    public function testWithBodyDoesNotModifyOriginalInstance()
    {
        $request = new Request('POST', 'https://example.com', [], '{"original":"body"}');
        $newBody = new StringStream('{"updated":"body"}');
        $newRequest = $request->withBody($newBody);

        $this->assertEquals('{"original":"body"}', (string)$request->getBody());
        $this->assertEquals('{"updated":"body"}', (string)$newRequest->getBody());
    }

    /**
     * Tests that withBody accepts a valid StreamInterface implementation.
     */
    public function testWithBodyHandlesStreamInterfaceImplementation()
    {
        $request = new Request('POST', 'https://example.com');
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('__toString')->willReturn('mocked body');

        $newRequest = $request->withBody($mockStream);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($mockStream, $newRequest->getBody());
        $this->assertEquals('mocked body', (string)$newRequest->getBody());
    }

    /**
     * Tests that getBody reflects changes made by withBody.
     */
    public function testGetBodyReflectsChangesByWithBody()
    {
        $request = new Request('POST', 'https://example.com');
        $newRequest = $request->withBody(new StringStream('{"message":"changed"}'));

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('{"message":"changed"}', (string)$newRequest->getBody());
    }

    /**
     * Tests that withAddedHeader adds a header if it doesn't exist.
     */
    public function testWithAddedHeaderAddsNewHeader()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withAddedHeader('X-Custom-Header', 'value1');

        $this->assertNotSame($request, $newRequest);
        $this->assertEmpty($request->getHeader('X-Custom-Header'));
        $this->assertEquals(['value1'], $newRequest->getHeader('X-Custom-Header'));
    }

    /**
     * Tests that withAddedHeader appends to an existing header.
     */
    public function testWithAddedHeaderAppendsToExistingHeader()
    {
        $request = new Request('GET', 'https://example.com', ['X-Custom-Header' => 'value1']);
        $newRequest = $request->withAddedHeader('X-Custom-Header', 'value2');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['value1'], $request->getHeader('X-Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $newRequest->getHeader('X-Custom-Header'));
    }

    /**
     * Tests that withAddedHeader handles case-insensitivity of header names.
     */
    public function testWithAddedHeaderHandlesCaseInsensitivity()
    {
        $request = new Request('GET', 'https://example.com', ['X-Custom-Header' => 'value1']);
        $newRequest = $request->withAddedHeader('x-custom-header', 'value2');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['value1'], $request->getHeader('X-Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $newRequest->getHeader('X-Custom-Header'));
    }

    /**
     * Tests that withAddedHeader does not modify the original instance.
     */
    public function testWithAddedHeaderDoesNotModifyOriginalInstance()
    {
        $request = new Request('GET', 'https://example.com', ['X-Custom-Header' => 'value1']);
        $newRequest = $request->withAddedHeader('X-Custom-Header', 'value2');

        $this->assertEquals(['value1'], $request->getHeader('X-Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $newRequest->getHeader('X-Custom-Header'));
    }

    /**
     * Tests that withAddedHeader handles multiple values correctly.
     */
    public function testWithAddedHeaderHandlesMultipleValues()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withAddedHeader('X-Custom-Header', ['value1', 'value2']);

        $this->assertNotSame($request, $newRequest);
        $this->assertEmpty($request->getHeader('X-Custom-Header'));
        $this->assertEquals(['value1', 'value2'], $newRequest->getHeader('X-Custom-Header'));
    }

    public function testRequestBasics()
    {
        $request = new Request('POST', 'https://example.com');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody(new StringStream('{"msg":"ok"}'));

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://example.com', (string)$request->getUri());
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));
        $this->assertEquals('{"msg":"ok"}', (string)$request->getBody());
    }

    public function testWithUri()
    {
        $originalUri = $this->createMock(UriInterface::class);
        $newUri = $this->createMock(UriInterface::class);

        $request = new Request('GET', $originalUri);

        $newRequest = $request->withUri($newUri);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame($originalUri, $request->getUri());
        $this->assertSame($newUri, $newRequest->getUri());
    }

    /**
     * Tests that withMethod updates the HTTP method and creates a new instance.
     */
    public function testWithMethodUpdatesMethodAndCreatesNewInstance()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withMethod('POST');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('POST', $newRequest->getMethod());
    }

    /**
     * Tests that withMethod maintains case insensitivity for the method name.
     */
    public function testWithMethodMaintainsCaseInsensitivity()
    {
        $request = new Request('get', 'https://example.com');
        $newRequest = $request->withMethod('pUt');

        $this->assertEquals('PUT', $newRequest->getMethod());

        $requestAnother = $newRequest->withMethod('post');
        $this->assertEquals('POST', $requestAnother->getMethod());
    }

    /**
     * Tests that withMethod does not modify the original request instance.
     */
    public function testWithMethodDoesNotModifyOriginalInstance()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withMethod('DELETE');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('DELETE', $newRequest->getMethod());
    }

    /**
     * Tests that withRequestTarget updates the request target correctly and creates a new instance.
     */
    public function testWithRequestTargetUpdatesTargetAndCreatesNewInstance()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withRequestTarget('/custom/target');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('/custom/target', $newRequest->getRequestTarget());
        $this->assertEquals('https://example.com', $request->getRequestTarget());
    }

    /**
     * Tests that withProtocolVersion sets a new protocol version and creates a new instance.
     */
    public function testWithProtocolVersionSetsNewProtocolAndCreatesNewInstance()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withProtocolVersion('2.0');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('2.0', $newRequest->getProtocolVersion());
        $this->assertEquals('1.1', $request->getProtocolVersion());
    }

    /**
     * Tests that withProtocolVersion does not modify the original instance.
     */
    public function testWithProtocolVersionDoesNotModifyOriginalInstance()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withProtocolVersion('1.0');

        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertEquals('1.0', $newRequest->getProtocolVersion());
    }

    /**
     * Tests that the request target defaults to the URI if not explicitly set.
     */
    public function testGetRequestTargetDefaultsToUri()
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertEquals('https://example.com', $request->getRequestTarget());
    }

    /**
     * Tests that withRequestTarget throws an exception for invalid request targets.
     */
    public function testWithRequestTargetThrowsForInvalidTargets()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The request target must be a non-empty string');

        $request = new Request('GET', 'https://example.com');
        $request->withRequestTarget('');
    }

    /**
     * Tests that hasHeader correctly identifies whether a header exists.
     */
    public function testHasHeader()
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/xml'
        ]);

        // Test for existing headers
        $this->assertTrue($request->hasHeader('Content-Type'));
        $this->assertTrue($request->hasHeader('Accept'));

        // Test for case insensitivity
        $this->assertTrue($request->hasHeader('content-type'));
        $this->assertTrue($request->hasHeader('ACCEPT'));

        // Test for non-existing headers
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    /**
     * Tests that withHeader replaces an existing header and creates a new instance.
     */
    public function testWithHeaderReplacesHeaderAndCreatesNewInstance()
    {
        $request = new Request('GET', 'https://example.com', ['Content-Type' => 'application/json']);
        $newRequest = $request->withHeader('Content-Type', 'text/plain');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));
        $this->assertEquals(['text/plain'], $newRequest->getHeader('Content-Type'));
    }

    /**
     * Tests that withHeader does not modify the original request's headers.
     */
    public function testWithHeaderDoesNotModifyOriginalInstance()
    {
        $request = new Request('GET', 'https://example.com', ['Content-Type' => 'application/json']);
        $newRequest = $request->withHeader('Content-Type', 'text/xml');

        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));
        $this->assertEquals(['text/xml'], $newRequest->getHeader('Content-Type'));
    }

    /**
     * Tests that withHeader correctly handles multiple values.
     */
    public function testWithHeaderHandlesMultipleValues()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withHeader('Accept', ['application/json', 'application/xml']);

        $this->assertNotSame($request, $newRequest);
        $this->assertEmpty($request->getHeader('Accept'));
        $this->assertEquals(['application/json', 'application/xml'], $newRequest->getHeader('Accept'));
    }

    /**
     * Tests that withoutHeader removes an existing header.
     */
    public function testWithoutHeaderRemovesExistingHeader()
    {
        $request = new Request('GET', 'https://example.com', ['X-Test-Header' => 'value']);
        $newRequest = $request->withoutHeader('X-Test-Header');

        $this->assertNotSame($request, $newRequest);
        $this->assertTrue($request->hasHeader('X-Test-Header'));
        $this->assertFalse($newRequest->hasHeader('X-Test-Header'));
    }

    /**
     * Tests that withoutHeader retains other headers when removing a target header.
     */
    public function testWithoutHeaderRetainsUnmodifiedHeaders()
    {
        $request = new Request('GET', 'https://example.com', [
            'X-Test-Header' => 'value',
            'X-Another-Header' => 'another-value'
        ]);
        $newRequest = $request->withoutHeader('X-Test-Header');

        $this->assertNotSame($request, $newRequest);
        $this->assertTrue($request->hasHeader('X-Test-Header'));
        $this->assertFalse($newRequest->hasHeader('X-Test-Header'));
        $this->assertEquals(['another-value'], $newRequest->getHeader('X-Another-Header'));
    }

    /**
     * Tests that withoutHeader handles case-insensitive header names for removal.
     */
    public function testWithoutHeaderCaseInsensitiveHeaderRemoval()
    {
        $request = new Request('GET', 'https://example.com', ['X-Test-Header' => 'value']);
        $newRequest = $request->withoutHeader('x-test-header');

        $this->assertNotSame($request, $newRequest);
        $this->assertTrue($request->hasHeader('X-Test-Header'));
        $this->assertFalse($newRequest->hasHeader('X-Test-Header'));
    }

    /**
     * Tests that withoutHeader does not throw an error when the header does not exist.
     */
    public function testWithoutHeaderDoesNotThrowForNonExistentHeader()
    {
        $request = new Request('GET', 'https://example.com');
        $newRequest = $request->withoutHeader('Non-Existent-Header');

        $this->assertNotSame($request, $newRequest);
        $this->assertFalse($newRequest->hasHeader('Non-Existent-Header'));
    }

    /**
     * Tests that withoutHeader does not modify the original request instance.
     */
    public function testWithoutHeaderPreservesOriginalRequestIntegrity()
    {
        $request = new Request('GET', 'https://example.com', ['X-Test-Header' => 'value']);
        $newRequest = $request->withoutHeader('X-Test-Header');

        $this->assertTrue($request->hasHeader('X-Test-Header'));
        $this->assertFalse($newRequest->hasHeader('X-Test-Header'));
    }

    /**
     * Tests that getHeaderLine returns a concatenated string of header values for a given header.
     */
    public function testGetHeaderLineReturnsConcatenatedHeaderValues()
    {
        $request = new Request('GET', 'https://example.com', [
            'Custom-Header' => ['value1', 'value2', 'value3']
        ]);

        $this->assertEquals('value1, value2, value3', $request->getHeaderLine('Custom-Header'));
    }

    /**
     * Tests that getHeaderLine returns an empty string for a header that does not exist.
     */
    public function testGetHeaderLineReturnsEmptyStringForNonExistentHeader()
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertEquals('', $request->getHeaderLine('Non-Existent-Header'));
    }

    /**
     * Tests that getHeaderLine handles case-insensitivity for the header name.
     */
    public function testGetHeaderLineHandlesCaseInsensitivity()
    {
        $request = new Request('GET', 'https://example.com', [
            'Custom-Header' => ['value1', 'value2']
        ]);

        $this->assertEquals('value1, value2', $request->getHeaderLine('custom-header'));
        $this->assertEquals('value1, value2', $request->getHeaderLine('CUSTOM-HEADER'));
    }
}
