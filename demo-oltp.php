<?php

require 'vendor/autoload.php';

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Tracer;

$exporter = new OtlpDebugExporter('http://localhost:4318/v1/traces');
$processor = new SimpleSpanProcessor($exporter, 'php56-demo');
$tracer = new Tracer($processor);

$tracer->startActiveSpan('demo.parent', function($parent) use ($tracer) {
    $parent->setAttribute('http.method', 'GET');
    $parent->setAttribute('env', 'demo');

    $tracer->startActiveSpan('demo.child', function($child) {
        $child->setAttribute('sql.query', 'SELECT * FROM users');
        usleep(10000);
    });

    $parent->addEvent('processing.done', ['duration.ms' => 10]);
});
