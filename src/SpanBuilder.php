<?php

namespace Curlmetry;

use Curlmetry\Utils\FromJsonUtils;
use Curlmetry\Utils\JsonUtils;

/**
 * SpanBuilder is responsible for constructing a span within a tracing system.
 * It allows configuration of span attributes, parent relationships,
 * starting timestamp, and kind.
 *
 * Implements the JsonSerializable interface to support serialization to JSON format.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class SpanBuilder implements \JsonSerializable
{
    /** @var string */
    private $name;
    /** @var Tracer  */
    private $tracer;
    /** @var Span|null  */
    private $explicitParent = null;
    /** @var bool  */
    private $noParent = false;
    /** @var array  */
    private $attributes = [];
    /** @var float|null  */
    private $startTime = null;
    /** @var 'INTERNAL'|'SERVER'|'CLIENT'|'PRODUCER'|'CONSUMER'  */
    private $kind = 'INTERNAL';

    /**
     * @param              $name
     * @param Tracer $tracer
     */
    public function __construct($name, Tracer $tracer)
    {
        $this->name = $name;
        $this->tracer = $tracer;
    }

    /**
     * @param Span $span
     *
     * @return $this
     */
    public function setParent(Span $span)
    {
        $this->explicitParent = $span;
        return $this;
    }

    /**
     * @return $this
     */
    public function setNoParent()
    {
        $this->noParent = true;
        return $this;
    }

    /**
     * @param float $timestamp
     *
     * @return $this
     */
    public function setStartTimestamp($timestamp)
    {
        $this->startTime = $timestamp;
        return $this;
    }

    /**
     * @param string $key
     * @param scalar $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param 'INTERNAL'|'SERVER'|'CLIENT'|'PRODUCER'|'CONSUMER' $kind
     *
     * @return $this
     */
    public function setSpanKind($kind)
    {
        $this->kind = $kind;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return Span|null
     */
    public function getExplicitParent()
    {
        return $this->explicitParent;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float|null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return Tracer
     */
    public function getTracer()
    {
        return $this->tracer;
    }

    /**
     * @return bool
     */
    public function hasNoParent()
    {
        return $this->noParent;
    }


    public function startSpan()
    {
        return $this->tracer->startSpanFromBuilder($this);
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'name'           => $this->name,
            'tracer'         => $this->tracer,
            'tracerClass'    => get_class($this->tracer),
            'explicitParent' => $this->explicitParent,
            'noParent'       => $this->noParent,
            'attributes'     => $this->attributes,
            'startTime'      => $this->startTime,
            'kind'           => $this->kind
        ];
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @param $json
     *
     * @return self
     */
    public static function fromJson($json)
    {
        $json = new JsonUtils($json);
        $tracer = FromJsonUtils::buildInstance(
            $json->getValue('tracerClass'),
            $json->getValue('tracer'),
            Tracer::class
        );
        $instance = new static(
            $json->getValue('name'),
            $tracer
        );
        $instance->explicitParent = $json->getValue('explicitParent');
        $instance->noParent       = $json->getValue('noParent');
        $instance->startTime      = $json->getValue('startTime');
        $instance->attributes     = $json->getValue('tags');
        $instance->kind = $json->getValue('kind');
        return $instance;
    }
}
