<?php

namespace Curlmetry;

/**
 * Provides a global tracer instance that can be set and retrieved.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class GlobalTracer
{
    /** @var Tracer */
    private static $tracer;

    /**
     * @param Tracer|null $tracer
     *
     * @return void
     */
    public static function set(Tracer $tracer = null)
    {
        self::$tracer = $tracer;
    }

    /**
     * @return Tracer|null
     */
    public static function get()
    {
        return self::$tracer;
    }
}
