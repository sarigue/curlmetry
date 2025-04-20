<?php

namespace Curlmetry;

/**
 * Defines a contract for objects that require a shutdown process.
 *
 * Implementations of this interface should provide logic for cleaning up
 * resources, closing connections, or performing any necessary finalization
 * tasks when the shutdown method is invoked.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
interface ShutdownInterface
{
    public function shutdown();
}
