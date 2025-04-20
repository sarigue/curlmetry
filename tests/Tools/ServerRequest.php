<?php

namespace Curlmetry\Test\Tools;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest implements ServerRequestInterface
{
    /** @var string */
    private $method;
    /** @var string */
    private $uri;
    /** @var array */
    private $headers = [];

    public function __construct($method, $uri, $headers = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
    }

    public function getProtocolVersion()
    {
        return '1.1';
    }

    public function withProtocolVersion($version)
    {
        return clone $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return false;
    }

    public function getHeader($name)
    {
        return null;
    }

    public function getHeaderLine($name)
    {
        return null;
    }

    public function withHeader($name, $value)
    {
        return clone $this;
    }

    public function withAddedHeader($name, $value)
    {
        return clone $this;
    }

    public function withoutHeader($name)
    {
        return clone $this;
    }

    public function getBody()
    {
        return null;
    }

    public function withBody(StreamInterface $body)
    {
        return clone $this;
    }

    public function getRequestTarget()
    {
        return null;
    }

    public function withRequestTarget($requestTarget)
    {
        return clone $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        return clone $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        return clone $this;
    }

    public function getServerParams()
    {
        return null;
    }

    public function getCookieParams()
    {
        return null;
    }

    public function withCookieParams(array $cookies)
    {
        return clone $this;
    }

    public function getQueryParams()
    {
        return [];
    }

    public function withQueryParams(array $query)
    {
        return clone $this;
    }

    public function getUploadedFiles()
    {
        return [];
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        return clone $this;
    }

    public function getParsedBody()
    {
        return null;
    }

    public function withParsedBody($data)
    {
        return clone $this;
    }

    public function getAttributes()
    {
        return [];
    }

    public function getAttribute($name, $default = null)
    {
        return $default;
    }

    public function withAttribute($name, $value)
    {
        return clone $this;
    }

    public function withoutAttribute($name)
    {
        return clone $this;
    }
}
