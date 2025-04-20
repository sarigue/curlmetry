<?php

namespace Curlmetry;

use Curlmetry\Processor\SpanProcessorInterface;
use Curlmetry\Sampling\SamplerInterface;
use Curlmetry\Utils\FromJsonUtils;
use Curlmetry\Utils\JsonUtils;

/**
 * Provides functionality to manage tracers and coordinate sampling and span processing.
 * Implements ShutdownInterface to handle cleanup logic during application shutdown.
 * Also implements JsonSerializable for serialization functionalities.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class TracerProvider implements ShutdownInterface, \JsonSerializable
{
    /** @var SamplerInterface */
    private $sampler;
    /** @var SpanProcessorInterface  */
    private $processor;

    public function __construct(SamplerInterface $sampler, SpanProcessorInterface $processor)
    {
        $this->sampler   = $sampler;
        $this->processor = $processor;
    }

    /**
     * @param string $name
     * @param string $version
     * @param string $schemaUrl
     *
     * @return Tracer
     */
    public function getTracer($name = 'default', $version = null, $schemaUrl = null)
    {
        return new Tracer($this->processor, $this->sampler, $name, $version, $schemaUrl);
    }

    /**
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function registerShutdown()
    {
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * @return SpanProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @return SamplerInterface
     */
    public function getSampler()
    {
        return $this->sampler;
    }

    /**
     * @return void
     * @throws Exception\ContextException
     */
    public function shutdown()
    {
        while ($scope = Scope::current()) {
            $scope->detach();
            $span = $scope->getSpan();
            if (! $span->isEnded()) {
                $span->end();
            }
            $this->processor->onEnd($span);
        }
        if ($this->processor instanceof ShutdownInterface) {
            $this->processor->shutdown();
        }
    }

    /**
     * @return TracerProvider
     */
    public static function getGlobal()
    {
        return GlobalTracerProvider::get();
    }

    /**
     * @return $this
     */
    public function setAsGlobal()
    {
        GlobalTracerProvider::set($this);
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'sampler'        => $this->sampler,
            'samplerClass'   => get_class($this->sampler),
            'processor'      => $this->processor,
            'processorClass' => get_class($this->processor)
        ];
    }

    /**
     * @param $json
     *
     * @return static
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

        return new static($sampler, $processor);
    }
}
