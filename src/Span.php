<?php

namespace Curlmetry;

use Curlmetry\Utils\JsonUtils;

/**
 * Class representing a single span, which is a timed operation within a trace.
 * Implements JsonSerializable interface to support JSON representation.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Span implements \JsonSerializable
{
    /** @var string */
    public $traceId;
    /** @var string */
    public $spanId;
    /** @var string */
    public $parentSpanId;
    /** @var string */
    public $name;
    /** @var float */
    public $startTime;
    /** @var float */
    public $endTime;
    /** @var string[] */
    public $attributes = [];
    /** @var array{code: scalar, description: string}|null  */
    public $status = null;
    /** @var array<int, array{name: string, time: float, attributes: array}>  */
    public $events = [];
    /** @var bool */
    private $sampled = true;

    public function __construct($name, $traceId, $parentSpanId = null)
    {
        $this->name         = $name;
        $this->traceId      = $traceId;
        $this->spanId       = bin2hex(openssl_random_pseudo_bytes(8));
        $this->parentSpanId = $parentSpanId;
        $this->startTime    = round(microtime(true) * 1e6);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSampled()
    {
        return $this->sampled;
    }

    /**
     * @return bool
     */
    public function isEnded()
    {
        return $this->endTime > 0;
    }

    /**
     * @param $value
     *
     * @return void
     */
    public function setSampled($value)
    {
        $this->sampled = (bool)$value;
    }

    /**
     * @return Scope
     */
    public function attach()
    {
        Context::push($this);
        return new Scope($this);
    }

    /**
     * Alias of attach()
     *
     * @return Scope
     */
    public function activate()
    {
        return $this->attach();
    }

    /**
     * @return void
     */
    public function end()
    {
        $this->endTime = round(microtime(true) * 1e6);
    }

    /**
     * @param \Exception $e
     *
     * @return void
     */
    public function recordException(\Exception $e)
    {
        $this->addEvent('exception', [
            'exception.type'       => get_class($e),
            'exception.code'       => $e->getCode(),
            'exception.message'    => $e->getMessage(),
            'exception.stacktrace' => $e->getTraceAsString()
        ]);
        $this->setStatus('ERROR', $e->getMessage());
    }

    /**
     * @param $code
     * @param $description
     *
     * @return void
     */
    public function setStatus($code, $description = '')
    {
        $this->status = ['code' => $code, 'description' => $description];
    }

    /**
     * @param $name
     * @param $attributes
     *
     * @return void
     */
    public function addEvent($name, $attributes = [])
    {
        $this->events[] = [
            'name' => $name,
            'time' => round(microtime(true) * 1e6),
            'attributes' => $attributes
        ];
    }

    /**
     * @return array{
     *     name: string,
     *     traceId: string,
     *     spanId: string,
     *     parentSpanId: string,
     *     startTime: float,
     *     endTime: float,
     *     duration: float,
     *     tags: array<string, mixed>,
     *     status: array{code: int|string, description: string}|null,
     *     events: array<int, array{
     *         name: string,
     *         time: float,
     *         attributes: array<string, mixed>
     *     }>
     * }
     */
    public function toArray()
    {
        return array(
            'name'          => $this->name,
            'traceId'       => $this->traceId,
            'spanId'        => $this->spanId,
            'parentSpanId'  => $this->parentSpanId,
            'startTime'     => $this->startTime,
            'endTime'       => $this->endTime,
            'duration'      => $this->endTime - $this->startTime,
            'tags'          => $this->attributes,
            'status'        => $this->status,
            'events'        => $this->events
        );
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @param $json
     *
     * @return self
     */
    public static function fromJson($json)
    {
        $json = new JsonUtils($json);
        $instance = new static(
            $json->getValue('name'),
            $json->getValue('traceId')
        );
        $instance->spanId = $json->getValue('spanId');
        $instance->parentSpanId = $json->getValue('parentSpanId');
        $instance->startTime = $json->getValue('startTime');
        $instance->endTime = $json->getValue('endTime');
        $instance->attributes = $json->getValue('tags');
        $instance->status = $json->getValue('status');
        $instance->events = $json->getValue('events');
        return $instance;
    }
}
