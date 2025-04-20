<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Exception\ContextException;
use Curlmetry\Exporter\JaegerExporter;
use Curlmetry\Test\CurlmetryTestCase;
use Curlmetry\Test\Tools\OtlpDebugExporter;

class JaegerExporterTest extends CurlmetryTestCase
{
    public function testException()
    {
        $this->expectException(ContextException::class);
        $this->expectExceptionMessage('Service name can\'t be null');
        $exporter = (new OtlpDebugExporter('http://example.com'))->setOutput('/dev/null');
        $exporter->export([[]], null);
    }
    public function testBuildPayload()
    {
        $exporter = new JaegerExporter('http://localhost:14268/api/traces');
        $payloadMethod = new \ReflectionMethod($exporter, 'buildPayload');
        $payloadMethod->setAccessible(true);

        $spans = [[
            'traceId' => 'abc',
            'spanId' => '123',
            'operationName' => 'unit.test',
            'startTime' => 123,
            'endTime' => 456,
            'duration' => 333,
            'tags' => [],
            'events' => [[
                'name' => 'eventname',
                'time' => time(),
                'attributes' => [
                    'attr1' => 'value1'
                ]]
            ]
        ]];

        $result = $payloadMethod->invoke($exporter, $spans, "test-service");

        $this->assertArrayHasKey("process", $result);
        $this->assertArrayHasKey("spans", $result);
    }

    public function testBuildAttribute()
    {

        $exporter = new JaegerExporter('http://localhost:4318/v1/traces');
        $ref = new \ReflectionMethod($exporter, 'buildAttribute');
        $ref->setAccessible(true);

        $type_list = [
            [true, 'bool'],
            [false, 'bool'],
            [3, 'int64'],
            [3.14, 'float64'],
            ['string', 'string'],
            [null, 'string'],
            [base64_encode(json_encode(['hello', 'world'])), 'binary'],
            [['key' => 'value'], 'string'],
            [['entry1', 'entry2'], 'string'],
            [new \stdClass(), 'string']
        ];

        foreach ($type_list as $type) {
            $result = $ref->invoke($exporter, 'mykey', $type[0]);
            $this->assertArrayHasKey('key', $result);
            $this->assertArrayHasKey('type', $result);
            $this->assertArrayHasKey('value', $result);
            $this->assertEquals('mykey', $result['key']);
            $this->assertEquals($type[1], $result['type']);
            ;
            if (is_array($type[0])  || is_object($type[1])) {
                $this->assertIsArray(json_decode($result['value'], true));
            }
        }
    }
}
