<?php

namespace Curlmetry;

/**
 * Provides a global tracer instance that can be set and retrieved.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class GlobalTracerProvider
{
    /** @var TracerProvider */
    private static $provider;

    /**
     * @param Tracer $tracer
     *
     * @return void
     */
    public static function set(TracerProvider $provider = null)
    {
        self::$provider = $provider;
    }

    /**
     * @return TracerProvider|null
     */
    public static function get()
    {
        return self::$provider;
    }
}
