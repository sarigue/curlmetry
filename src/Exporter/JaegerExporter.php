<?php

namespace Curlmetry\Exporter;

use Curlmetry\Exception\ContextException;
use Curlmetry\Exporter;

/**
 * Handles the export of tracing data to Jaeger by building and formatting
 * payloads and attributes according to Jaeger's requirements.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class JaegerExporter extends Exporter
{
    /**
     * @param array $spans
     * @param       $serviceName
     *
     * @return array
     */
    protected function buildPayload(array $spans, $serviceName)
    {
        return [
            'process' => [
                'serviceName' => (string)$serviceName,
                'tags' => [
                    [
                        'key' => 'php.version',
                        'type' => 'string',
                        'value' => phpversion()
                    ]
                ]
            ],
            'spans' => $this->convertSpanAttributes($spans)
        ];
    }

    protected function buildAttribute($key, $value)
    {
        if (is_bool($value)) {
            $type = 'bool';
        } elseif (is_int($value)) {
            $type = 'int64';
        } elseif (is_float($value)) {
            $type = 'float64';
        } elseif (is_string($value)) {
            if (static::isBase64($value)) {
                $type = 'binary';
            } else {
                $type = 'string';
            }
        } elseif (is_array($value) || is_object($value)) {
            $type = 'string';
            $value = json_encode($value);
        } else {
            $type = 'string';
        }

        return [
            'key'   => $key,
            'type'  => $type,
            'value' => $value
        ];
    }
}
