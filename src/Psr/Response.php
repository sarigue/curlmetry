<?php

namespace Curlmetry\Psr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Represents an HTTP response.
 *
 * This class implements the `ResponseInterface` and provides methods
 * to manipulate HTTP status codes, headers, body, and protocol version.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Response implements ResponseInterface
{
    private $statusCode;
    private $headers = [];
    private $body;
    private $protocol = '1.1';
    private $reasonPhrase = '';

    public function __construct($statusCode = 200, array $headers = [], $body = '', $reasonPhrase = '')
    {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        foreach ($headers as $key => $value) {
            $this->headers[strtolower($key)] = (array) $value;
        }
        $this->body = $body instanceof StreamInterface ? $body : new StringStream($body);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
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
