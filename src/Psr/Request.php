<?php

namespace Curlmetry\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * The Request class implements the RequestInterface and provides methods to interact with
 * HTTP request data including method, URI, headers, body, and HTTP protocol version.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Request implements RequestInterface
{
    private $method;
    private $uri;
    private $headers = [];
    private $body;
    private $protocol = '1.1';
    private $requestTarget = '';

    public function __construct($method, $uri, array $headers = [], $body = '')
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        foreach ($headers as $name => $values) {
            $this->headers[strtolower($name)] = (array) $values;
        }
        $this->body = $body instanceof StreamInterface ? $body : new StringStream($body);
    }

    public function getRequestTarget()
    {
        if ($this->requestTarget !== '') {
            return $this->requestTarget;
        }

        return (string)$this->uri;
    }

    public function withRequestTarget($requestTarget)
    {
        if (!is_string($requestTarget) || trim($requestTarget) === '') {
            throw new \InvalidArgumentException('The request target must be a non-empty string');
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = strtoupper($method);
        return $clone;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;
        return $clone;
    }

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->protocol = $version;
        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name)
    {
        $key = strtolower($name);
        return isset($this->headers[$key]) ? $this->headers[$key] : [];
    }

    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)] = (array) $value;
        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $key = strtolower($name);
        if (!isset($clone->headers[$key])) {
            $clone->headers[$key] = [];
        }
        $clone->headers[$key] = array_merge($clone->headers[$key], (array) $value);
        return $clone;
    }

    public function withoutHeader($name)
    {
        $clone = clone $this;
        unset($clone->headers[strtolower($name)]);
        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
}
