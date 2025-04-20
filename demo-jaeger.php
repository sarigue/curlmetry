<?php

require 'vendor/autoload.php';

use Curlmetry\Processor\SimpleSpanProcessor;
use Curlmetry\Test\Tools\JaegerDebugExporter;
use Curlmetry\Tracer;

$exporter = new JaegerDebugExporter('http://localhost:14268/api/traces');
$processor = new SimpleSpanProcessor($exporter, 'php56-jaeger-demo');
$tracer = new Tracer($processor);

$tracer->startActiveSpan('jaeger.parent', function($parent) use ($tracer) {
    $parent->setAttribute('component', 'demo');
    $parent->setAttribute('http.status_code', 200);

    $tracer->startActiveSpan('jaeger.child', function($child) {
        $child->setAttribute('db.system', 'mysql');
        $child->setAttribute('db.statement', 'SELECT * FROM orders');
        usleep(5000);
    });

    $parent->addEvent('finalize', ['ok' => true]);
});
