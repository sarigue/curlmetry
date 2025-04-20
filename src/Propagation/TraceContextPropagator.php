<?php

namespace Curlmetry\Propagation;

use Curlmetry\Span;

/**
 * A utility class for propagating trace context using the W3C Trace Context standard.
 * This class provides functionality to inject and extract tracing information
 * into and from HTTP headers.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class TraceContextPropagator
{
    const TRACEPARENT_HEADER = 'traceparent';
    const VERSION = '00';
    const FLAG_SAMPLED = '01';
    const FLAG_NOT_SAMPLED = '00';

    /**
     * Inject trace context into headers.
     *
     * @param Span $span
     * @param array &$headers
     * @return bool
     */
    public static function inject(Span $span, array &$headers)
    {
        $version = self::VERSION;
        $traceId = $span->traceId;
        $spanId  = $span->spanId;
        $traceFlags = $span->isSampled() ? self::FLAG_SAMPLED : self::FLAG_NOT_SAMPLED;

        // Basic validation (optional)
        if (strlen($traceId) !== 32 || strlen($spanId) !== 16) {
            return false;
        }

        $headers[self::TRACEPARENT_HEADER] = "{$version}-{$traceId}-{$spanId}-{$traceFlags}";
        return true;
    }

    /**
     * Extracts trace context from headers
     *
     * @param array $headers
     * @return array|null
     */
    public static function extract(array $headers)
    {
        // Normalize keys for case-insensitive lookup
        $lowered = array_change_key_case($headers, CASE_LOWER);

        if (!isset($lowered[self::TRACEPARENT_HEADER])) {
            return null;
        }

        $traceparent = $lowered[self::TRACEPARENT_HEADER];

        if (is_array($traceparent)) {
            $traceparent = implode(',', $traceparent);
        }

        $value = $traceparent;
        $parts = explode('-', $value);
        if (count($parts) === 4 && strlen($parts[1]) === 32 && strlen($parts[2]) === 16) {
            return [
                'version'    => $parts[0],
                'traceId'    => $parts[1],
                'spanId'     => $parts[2],
                'traceFlags' => $parts[3],
            ];
        }

        return null;
    }
}
