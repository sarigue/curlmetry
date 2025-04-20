<?php

/** @noinspection ALL */

namespace Curlmetry\Test;

use Curlmetry\Context;

class CurlmetryTestCaseBase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Context::clear();
        parent::setUp();
    }
}
