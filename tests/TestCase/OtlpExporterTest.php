<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Exporter\OtlpExporter;
use Curlmetry\Test\CurlmetryTestCase;

class OtlpExporterTest extends CurlmetryTestCase
{
    public function testHeadersIncludeContentType()
    {
        $exporter = new OtlpExporter('http://localhost:4318/v1/traces');
        $ref = new \ReflectionMethod($exporter, 'buildHeaders');
        $ref->setAccessible(true);
        $headers = $ref->invoke($exporter);

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testBuildAttribute()
    {

        $exporter = new OtlpExporter('http://localhost:4318/v1/traces');
        $ref = new \ReflectionMethod($exporter, 'buildAttribute');
        $ref->setAccessible(true);

        $type_list = [
            [true, 'boolValue'],
            [false, 'boolValue'],
            [3, 'intValue'],
            [3.14, 'doubleValue'],
            ['string', 'stringValue'],
            [null, 'stringValue'],
            [base64_encode(json_encode(['hello', 'world'])), 'bytesValue'],
            [['key' => 'value'], 'kvlistValue'],
            [['entry1', 'entry2'], 'arrayValue'],
            [new \stdClass(), 'kvlistValue']
        ];

        foreach ($type_list as $type) {
            $result = $ref->invoke($exporter, 'mykey', $type[0]);
            $this->assertArrayHasKey('key', $result);
            $this->assertArrayHasKey('value', $result);
            $this->assertEquals('mykey', $result['key']);
            $this->assertIsArray($result['value']);
            $this->assertArrayHasKey($type[1], $result['value']);
            ;
        }
    }
}
