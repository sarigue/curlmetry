<?php

namespace Curlmetry\Processor;

use Curlmetry\Context;
use Curlmetry\Exporter\ExporterInterface;
use Curlmetry\Span;
use Curlmetry\Utils\FromJsonUtils;
use Curlmetry\Utils\JsonUtils;
use Exception;

/**
 * SimpleSpanProcessor handles the processing of spans by exporting them to the configured
 * exporter and allowing serialization of its configuration for reuse.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class SimpleSpanProcessor implements SpanProcessorInterface, \JsonSerializable
{
    /** @var ExporterInterface */
    private $exporter;
    /** @var string */
    private $serviceName;

    public function __construct(ExporterInterface $exporter, $serviceName)
    {
        $this->exporter = $exporter;
        $this->serviceName = $serviceName;
    }

    /**
     * @param Span $span
     *
     * @return void
     * @throws Exception
     */
    public function onEnd(Span $span)
    {
        $this->exporter->export([$span->toArray()], $this->serviceName);
    }


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'serviceName'   => $this->serviceName,
            'exporter'      => $this->exporter,
            'exporterClass' => get_class($this->exporter),
        ];
    }

    /**
     * @param $json
     *
     * @return self
     */
    public static function fromJson($json)
    {
        $json = JsonUtils::create($json);
        $exporter = FromJsonUtils::buildInstance(
            $json->getValue('exporterClass'),
            $json->getValue('exporter'),
            ExporterInterface::class
        );
        return new static($exporter, $json->getValue('serviceName'));
    }
}
