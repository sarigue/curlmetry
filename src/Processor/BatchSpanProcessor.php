<?php

namespace Curlmetry\Processor;

use Curlmetry\Exporter\ExporterInterface;
use Curlmetry\ShutdownInterface;
use Curlmetry\Span;
use Curlmetry\Utils\FromJsonUtils;
use Curlmetry\Utils\JsonUtils;
use Exception;

/**
 * Class BatchSpanProcessor
 *
 * Implements a span processor that batches spans and exports them in bulk.
 * The processor collects spans in memory until a specified batch size is reached
 * or an explicit flush is triggered, then exports the spans using the configured exporter.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class BatchSpanProcessor implements SpanProcessorInterface, ShutdownInterface, \JsonSerializable
{
    private $exporter;
    private $serviceName;
    private $batch = [];
    private $maxBatchSize = 10;

    /**
     * @param ExporterInterface $exporter
     * @param string            $serviceName
     * @param int               $maxBatchSize
     */
    public function __construct(ExporterInterface $exporter, $serviceName, $maxBatchSize = 10)
    {
        $this->exporter = $exporter;
        $this->serviceName = $serviceName;
        $this->maxBatchSize = $maxBatchSize;
    }

    /**
     * @param Span $span
     *
     * @return void
     * @throws Exception
     */
    public function onEnd(Span $span)
    {
        $this->batch[] = $span->toArray();

        if (count($this->batch) >= $this->maxBatchSize || $span->parentSpanId === null) {
            $this->flush();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function flush()
    {
        if (!empty($this->batch)) {
            $this->exporter->export($this->batch, $this->serviceName);
            $this->batch = [];
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function shutdown()
    {
        $this->flush();
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'exporter'      => $this->exporter,
            'exporterClass' => get_class($this->exporter),
            'serviceName'   => $this->serviceName,
            'batch'         => $this->batch,
            'maxBatchSize'  => $this->maxBatchSize
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
        $exporter = FromJsonUtils::buildInstance(
            $json->getValue('exporterClass'),
            $json->getValue('exporter'),
            ExporterInterface::class
        );
        $instance = new static(
            $exporter,
            (string)$json->getValue('serviceName'),
            (int)$json->getValue('maxBatchSize')
        );
        $instance->batch = $json->getValue('batch', []);
        return $instance;
    }
}
