<?php

namespace Curlmetry;

use Curlmetry\Utils\JsonUtils;

/**
 * Class Context
 *
 * Provides a static utility for managing and tracking a stack of Span objects with an optional trace ID.
 * This class is designed to be used in scenarios where spans and trace information
 * need to be persisted, retrieved, or serialized.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Context
{
    /** @var Span[] */
    private static $stack = [];
    /** @var string|null */
    private static $traceId = null;

    /**
     * @param string $traceId
     *
     * @return void
     */
    public static function setTraceId($traceId)
    {
        self::$traceId = $traceId;
        foreach (self::$stack as $span) {
            $span->traceId = $traceId;
        }
    }

    /**
     * @return string|null
     */
    public static function getTraceId()
    {
        return self::$traceId;
    }

    /**
     * @param Span $span
     *
     * @return void
     */
    public static function push(Span $span)
    {
        self::$stack[] = $span;
    }

    /**
     * @return Span|null
     */
    public static function pop()
    {
        return array_pop(self::$stack);
    }

    /**
     * @return Span|false
     */
    public static function current()
    {
        return end(self::$stack) ?: null;
    }

    /**
     * @return void
     */
    public static function clear()
    {
        self::$traceId = null;
        self::$stack = [];
    }

    /**
     * @return array{traceId: string, stack: Span[]}
     */
    public static function toArray()
    {
        return [
            'traceId' => self::$traceId,
            'stack'   => self::$stack
        ];
    }

    /**
     * @return false|string
     */
    public static function saveToJson()
    {
        return json_encode(static::toArray());
    }

    public static function restoreFromJson($json)
    {
        $json = new JsonUtils($json);
        self::$traceId = $json->getValue('traceId');
        self::$stack   = [];
        foreach ($json->getValue('stack') as $span) {
            self::$stack[] = Span::fromJson($span);
        }
    }
}
