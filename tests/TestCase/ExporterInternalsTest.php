<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Test\Tools\OtlpDebugExporter;
use Curlmetry\Test\CurlmetryTestCase;

class ExporterInternalsTest extends CurlmetryTestCase
{
    public function testToString()
    {
        $exporter = (new OtlpDebugExporter('http://localhost'))->setOutput('/dev/null');
        $this->assertStringContainsString('endpoint', (string)$exporter);
    }

    public function testIsList()
    {
        $r = new \ReflectionClass(OtlpDebugExporter::class);
        $m = $r->getMethod('isList');
        $m->setAccessible(true);

        $this->assertTrue($m->invoke(null, [["x" => 1], ["x" => 2]]));
        $this->assertFalse($m->invoke(null, ["a" => ["x" => 1]]));
    }

    public function testIsBase64()
    {
        $r = new \ReflectionClass(OtlpDebugExporter::class);
        $m = $r->getMethod("isBase64");
        $m->setAccessible(true);

        $valid = base64_encode("hello");
        $this->assertTrue($m->invoke(null, $valid));
        $this->assertFalse($m->invoke(null, "not-base64!!"));
        $this->assertFalse($m->invoke(null, "aze"));
    }
}
