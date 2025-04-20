<?php

namespace Curlmetry;

/**
 * Provides a global tracer instance that can be set and retrieved.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Globals
{
    public static function setTracerProvider(TracerProvider $provider = null)
    {
        GlobalTracerProvider::set($provider);
    }

    public static function tracerProvider()
    {
        return GlobalTracerProvider::get();
    }

    public static function setTracer(Tracer $tracer = null)
    {
        GlobalTracer::set($tracer);
    }

    public static function tracer()
    {
        return GlobalTracer::get();
    }
}
