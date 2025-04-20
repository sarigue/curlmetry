<?php

namespace Curlmetry;

use Curlmetry\Exception\ContextException;
use Curlmetry\Processor\SpanProcessorInterface;
use Curlmetry\Sampling\SamplerInterface;
use Curlmetry\Utils\FromJsonUtils;
use Curlmetry\Utils\JsonUtils;

/**
 * Tracer is responsible for creating and managing spans, which are used for tracing operations.
 * It relies on a configured SpanProcessor and Sampler to process spans and determine sampling.
 * It provides utility methods for starting and ending spans, as well as handling context injection.
 * Additionally, it can serialize and deserialize its configuration and state via JSON.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Tracer implements \JsonSerializable
{
    /** @var string  */
    private $traceId;
    /** @var SpanProcessorInterface */
    private $processor;
    /** @var SamplerInterface */
    private $sampler;
    /** @var string */
    private $name;
    /** @var string */
    private $version;
    /** @var string */
    private $schemaUrl;

    /**
     * @param SpanProcessorInterface $processor
     */
    public function __construct(
        SpanProcessorInterface $processor,
        SamplerInterface $sampler,
        $name = 'default',
        $version = null,
        $schemaUrl = null
    ) {
        $this->traceId   = bin2hex(openssl_random_pseudo_bytes(16));
        $this->processor = $processor;
        $this->sampler   = $sampler;
        $this->name      = $name;
        $this->version   = $version;
        $this->schemaUrl = $schemaUrl;
    }

    /**
     * @return Tracer
     */
    public static function getGlobal()
    {
        return GlobalTracer::get();
    }

    /**
     * @return $this
     */
    public function setAsGlobal()
    {
        GlobalTracer::set($this);
        return $this;
    }

    /**
     * @param $name
     *
     * @return SpanBuilder
     */
    public function spanBuilder($name)
    {
        return new SpanBuilder($name, $this);
    }

    /**
     * @param $name
     * @param $callback
     *
     * @return Span
     * @throws ContextException
     */
    public function startActiveSpan($name, $callback)
    {
        $span = $this->spanBuilder($name)->startSpan();
        $scope = $span->attach();

        try {
            $callback($span);
        } catch (\Exception $e) {
            $span->recordException($e);
            $span->setStatus('ERROR', $e->getMessage());
            throw $e;
        } finally {
            $scope->detach();
            $span->end();
        }

        return $span;
    }

    /**
     * @param $name
     *
     * @return Span
     */
    public function startSpan($name)
    {
        return $this->spanBuilder($name)->startSpan();
    }

    /**
     * @param Span $span
     *
     * @return void
     * @throws ContextException
     */
    public function endSpan(Span $span)
    {
        $span->end();
        $scope = Scope::current();
        if ($scope && $scope->getSpan() === $span) {
            $scope->detach();
        }
        $this->processor->onEnd($span);
    }

    /**
     * @param SpanBuilder $builder
     *
     * @return Span
     */
    public function startSpanFromBuilder(SpanBuilder $builder)
    {
        $noParent        = $builder->hasNoParent();
        $explicitParent  = $builder->getExplicitParent();
        $name            = $builder->getName();
        $startTime       = $builder->getStartTime();
        $attributes      = $builder->getAttributes();
        $kind            = $builder->getKind();
        $traceId         = Context::getTraceId() ?: $this->traceId;

        if ($noParent) {
            $parentId = null;
        } elseif ($explicitParent !== null) {
            $parentId = $explicitParent->spanId;
        } else {
            $parent = Context::current();
            $parentId = $parent ? $parent->spanId : null;
        }

        $span = new Span($name, $traceId, $parentId);
        $span->setSampled($this->sampler->shouldSample($builder));

        if ($startTime !== null) {
            $span->startTime = $startTime;
        }

        foreach ($attributes as $key => $value) {
            $span->attributes[$key] = $value;
        }

        $span->setAttribute('span.kind', $kind);
        return $span;
    }

    /**
     * Override the trace ID for this tracer instance (manual context injection)
     *
     * @param string $traceId
     * @return $this Clone of instance
     */
    public function withTraceId($traceId)
    {
        $clone = clone $this;
        $clone->traceId = $traceId;
        return $clone;
    }

    public function getTraceId()
    {
        return $this->traceId;
    }

    public function getSampler()
    {
        return $this->sampler;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSchemaUrl()
    {
        return $this->schemaUrl;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'traceId'        => $this->traceId,
            'processor'      => $this->processor,
            'processorClass' => get_class($this->processor),
            'sampler'        => $this->sampler,
            'samplerClass'   => get_class($this->sampler),
            'name'           => $this->name,
            'version'        => $this->version,
            'schemaUrl'      => $this->schemaUrl
        ];
    }

    /**
     * @param $json
     *
     * @return self
     */
    public static function fromJson($json)
    {
        $json = new JsonUtils($json);
        $samplerClass   = $json->getValue('samplerClass');
        $samplerData    = $json->getValue('sampler');
        $processorClass = $json->getValue('processorClass');
        $processorData  = $json->getValue('processor');

        $sampler = FromJsonUtils::buildInstance($samplerClass, $samplerData, SamplerInterface::class);
        $processor = FromJsonUtils::buildInstance($processorClass, $processorData, SpanProcessorInterface::class);

        $instance = new static($processor, $sampler);
        $instance->traceId   = $json->getValue('traceId');
        $instance->name      = $json->getValue('name');
        $instance->version   = $json->getValue('version');
        $instance->schemaUrl = $json->getValue('schemaUrl');
        return $instance;
    }
}
