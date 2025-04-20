<?php

namespace Curlmetry\Exporter;

/**
 * Represents an interface for exporting tracing spans to an external system.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
interface ExporterInterface
{
    /**
     * @param array<int, array{
     *     traceId: string,
     *     spanId: string,
     *     parentSpanId: string,
     *     operationName: string,
     *     startTime: float,
     *     endTime: float,
     *     duration: float,
     *     tags: array<int, array{
     *         key: string,
     *         type: 'string'|'int'|'bool'|'float'|'double'|'array'|'binary',
     *         value: scalar
     *     }>,
     *     status: array{code: int|string, description: string},
     *     events: array<int, array{
     *         name: string,
     *         time: float,
     *         attributes: array<string, mixed>
     *     }>
     * }> $spans
     * @param string $serviceName
     *
     * @return void
     * @throws \Exception
     */
    public function export(array $spans, $serviceName);
}
