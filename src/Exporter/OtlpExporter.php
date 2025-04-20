<?php

namespace Curlmetry\Exporter;

use Curlmetry\Exception\ContextException;
use Curlmetry\Exporter;

/**
 * The OtlpExporter class is responsible for exporting OpenTelemetry data
 * by building payloads and converting attributes into structured formats.
 * It extends the base Exporter class.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class OtlpExporter extends Exporter
{
    public function buildPayload(array $spans, $serviceName)
    {
        return [
            'resourceSpans' => [
                [
                    'resource' => [
                        'attributes' => [
                            [
                                'key' => 'service.name',
                                'value' => ['stringValue' => (string)$serviceName]
                            ]
                        ]
                    ],
                    'scopeSpans' => [
                        [
                            'scope' => ['name' => 'manual-instrumentation'],
                            'spans' => $this->convertSpanAttributes($spans)
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function buildAttribute($key, $value)
    {
        if (is_bool($value)) {
            $val = ['boolValue' => $value];
        } elseif (is_int($value)) {
            $val = ['intValue' => $value];
        } elseif (is_float($value)) {
            $val = ['doubleValue' => $value];
        } elseif (is_string($value)) {
            if (static::isBase64($value)) {
                $val = ['bytesValue' => $value];
            } else {
                $val = ['stringValue' => $value];
            }
        } elseif (is_array($value)) {
            if (static::isList($value)) {
                $val = ['arrayValue' => $value];
            } else {
                $val = ['kvlistValue' => $value];
            }
        } elseif (is_object($value)) {
            $val = ['kvlistValue' => method_exists($value, 'toArray')  ? $value->toArray() : (array)$value];
        } else {
            $val = ['stringValue' => (string)$value];
        }

        return [
            'key'   => $key,
            'value' => $val
        ];
    }
}
