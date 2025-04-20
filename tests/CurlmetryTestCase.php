<?php

namespace Curlmetry\Test;

if (PHP_VERSION_ID < 70100) {
    require_once __DIR__ . '/CurlmetryTestCaseBase.v5.php';
} else {
    require_once __DIR__ . '/CurlmetryTestCaseBase.v7.php';
}

class CurlmetryTestCase extends CurlmetryTestCaseBase
{
    /**
     * AssertStringContains compatible with PHPUnit 5 to PHPUnit 9+
     *
     * @param string|iterable $haystack
     * @param string|iterable $needle
     * @param string          $message
     *
     * @return void
     */
    protected function assertStringContainsCompat($needle, $haystack, $message = '')
    {
        // Not a string
        if (!is_string($needle)) {
            $this->assertContains($needle, $haystack, $message);
            return;
        }

        // PHPUnit 9+ - Use assertStringContainsString()
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString($needle, $haystack, $message);
            return;
        }

        // PHPUnit < 9
        $this->assertContains($needle, $haystack, $message);
    }

    /**
     * AssertStringNotContains compatible with PHPUnit 5 to PHPUnit 9+
     *
     * @param string|iterable $haystack
     * @param string|iterable $needle
     * @param string          $message
     *
     * @return void
     */
    protected function assertStringNotContainsCompat($needle, $haystack, $message = '')
    {
        // Not a string
        if (!is_string($needle)) {
            $this->assertNotContains($needle, $haystack, $message);
            return;
        }

        // PHPUnit 9+ - Use assertStringContainsString()
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString($needle, $haystack, $message);
            return;
        }

        // PHPUnit < 9
        $this->assertNotContains($needle, $haystack, $message);
    }

    /**
     * @param array $needles
     * @param       $haystack
     * @param       $message
     *
     * @return void
     */
    protected function assertStringContainsOneOf(array $needles, $haystack, $message = '')
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                $this->assertTrue(true); // success
                return;
            }
        }
        $this->fail($message ?: 'None of the expected substrings were found.');
    }

    /**
     * @param array $needles
     * @param       $haystack
     * @param       $message
     *
     * @return void
     */
    protected function assertStringContainsAllOf(array $needles, $haystack, $message = '')
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) === false) {
                $this->fail($message ?: 'Substring "' . $needle . '" does not exist"');
//                return;
            }
        }
        $this->assertTrue(true);
    }

    protected function assertStringNotContainsOneOf(array $needles, $haystack, $message = '')
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                $this->fail($message ?: 'One of the substrings were found.');
//                return;
            }
        }
        $this->assertTrue(true);
    }
}
