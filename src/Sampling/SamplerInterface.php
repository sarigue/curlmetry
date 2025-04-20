<?php

namespace Curlmetry\Sampling;

use Curlmetry\SpanBuilder;

/**
 * Interface SamplerInterface
 *
 * Defines the methods required for implementing a sampler.
 * Samplers are responsible for determining whether a span should be sampled.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
interface SamplerInterface
{
    /**
     * Returns true if the span should be sampled.
     *
     * @param SpanBuilder $builder
     * @return bool
     */
    public function shouldSample(SpanBuilder $builder);
}
