<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Span;
use Exception;
use Curlmetry\Test\CurlmetryTestCase;

class SpanCoverageTest extends CurlmetryTestCase
{
    public function testSpanAttributes()
    {
        $span = new Span('coverage.test', 'trace123', null);
        $span->setAttribute('user.id', 42);
        $span->setStatus('ERROR', 'Failure');
        $span->recordException(new Exception('boom', 123));
        /** @noinspection SqlNoDataSourceInspection */
        $span->addEvent('db.query', ['query' => 'SELECT * FROM users']);
        $span->end();

        $this->assertTrue($span->isEnded());
        $this->assertEquals("coverage.test", $span->name);
        $this->assertIsArray($span->attributes);
        $this->assertNotEmpty($span->events);
        $this->assertEquals('exception', $span->events[0]['name']);
        $this->assertEquals('Exception', $span->events[0]['attributes']['exception.type']);
        $this->assertEquals(123, $span->events[0]['attributes']['exception.code']);
        $this->assertEquals('boom', $span->events[0]['attributes']['exception.message']);
        $this->assertEquals('db.query', $span->events[1]['name']);
        /** @noinspection SqlNoDataSourceInspection */
        $this->assertEquals('SELECT * FROM users', $span->events[1]['attributes']['query']);
    }

    public function testToString()
    {
        $span = new Span('coverage.test', 'trace123', null);
        $this->assertIsString($span->__toString());
        $this->assertEquals(json_encode($span->toArray(), JSON_PRETTY_PRINT), (string)$span);
    }
}
