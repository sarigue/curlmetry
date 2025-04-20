<?php

namespace Curlmetry\Sampling;

use Curlmetry\SpanBuilder;

/**
 * Represents a sampler that always returns a positive sampling decision.
 * This is typically used to signify that all spans should be sampled.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class AlwaysOnSampler implements SamplerInterface, \JsonSerializable
{
    public function shouldSample(SpanBuilder $builder = null)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [];
    }
}
