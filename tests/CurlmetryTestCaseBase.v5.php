<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Curlmetry\Test;

use Curlmetry\Context;

class CurlmetryTestCaseBase extends \PHPUnit\Framework\TestCase
{
    /** @noinspection PhpHierarchyChecksInspection */
    protected function setUp()
    {
        Context::clear();
        parent::setUp();
    }
}
