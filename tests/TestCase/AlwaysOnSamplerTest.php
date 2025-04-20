<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Sampling\AlwaysOnSampler;
use Curlmetry\Test\CurlmetryTestCase;

class AlwaysOnSamplerTest extends CurlmetryTestCase
{
    public function testShouldSampleAlways()
    {
        $sampler = new AlwaysOnSampler();
        $this->assertTrue($sampler->shouldSample());
    }
}
