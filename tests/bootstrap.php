<?php

require __DIR__ . '/../vendor/autoload.php';

// Compatibility PHPUnit 5.x / 6.x+
if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
