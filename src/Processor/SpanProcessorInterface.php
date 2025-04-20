<?php

namespace Curlmetry\Processor;

use Curlmetry\Span;

/**
 * Interface that defines a span processor that processes ended spans.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
interface SpanProcessorInterface
{
    public function onEnd(Span $span);
}
