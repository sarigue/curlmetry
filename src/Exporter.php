<?php

namespace Curlmetry;

use Curlmetry\Exception\ContextException;
use Curlmetry\Exporter\ExporterInterface;
use Curlmetry\Utils\JsonUtils;
use Exception;

/**
 * Abstract class for exporting data to an endpoint.
 * Implements ExporterInterface and \JsonSerializable.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
abstract class Exporter implements ExporterInterface, \JsonSerializable
{
    protected $endpoint;

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param array  $spans
     * @param string $serviceName
     *
     * @return bool
     * @throws ContextException
     * @throws Exception
     */
    public function export(array $spans, $serviceName)
    {
        if (empty($serviceName)) {
            throw new ContextException('Service name can\'t be null');
        }
        return $this->send($this->buildPayload($spans, $serviceName));
    }

    /**
     * @param array  $spans
     * @param string $serviceName
     *
     * @return mixed
     */
    abstract protected function buildPayload(array $spans, $serviceName);

    /**
     * @param $payload
     *
     * @return true
     * @throws Exception
     * @codeCoverageIgnore
     */
    protected function send($payload)
    {
        $json = json_encode($payload);
        if ($json === false) {
            throw new Exception('JSON error :' . json_last_error_msg());
        }

        $headers = [];
        foreach ($this->buildHeaders($json) as $k => $v) {
            $headers[] = $k . ':' . $v;
        }
        /** @codeCoverageIgnoreStart */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error || $status >= 300) {
            throw new Exception("Export error - HTTP $status : $error\nResponse : $response");
        }
        /** @codeCoverageIgnoreEnd */

        return true;
    }

    /**
     * @param string $content
     *
     * @return array<string, string|int>
     */
    protected function buildHeaders($content = '')
    {
        return [
            'Content-Type'   => 'application/json',
            'Content-Length' => strlen($content)
        ];
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return ['endpoint' => $this->endpoint];
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @param $json
     *
     * @return static
     */
    public static function fromJson($json)
    {
        return new static(JsonUtils::create($json)->getValue('endpoint'));
    }

    /**
     * @param array<int, array{
     *     traceId: string,
     *     spanId: string,
     *     parentSpanId: string,
     *     operationName: string,
     *     startTime: float,
     *     endTime: float,
     *     duration: float,
     *     tags: array<string, mixed>,
     *     status: array{code: int|string, description: string},
     *     events: array<int, array{
     *         name: string,
     *         time: float,
     *         attributes: array<string, mixed>
     *     }>
     * }> $spans
     *
     * @return array
     */
    protected function convertSpanAttributes(array $spans)
    {
        foreach ($spans as & $span) {
            if (isset($span['tags'])) {
                foreach ($span['tags'] as $key => $value) {
                    $span['tags'][] = $this->buildAttribute($key, $value);
                    $span['tags'][$key] = null;
                }
                $span['tags'] = array_filter($span['tags']);
            }
            if (isset($span['events'])) {
                foreach ($span['events'] as & $event) {
                    if (isset($event['attributes'])) {
                        foreach ($event['attributes'] as $key => $value) {
                            $event['attributes'][] = $this->buildAttribute($key, $value);
                            $event['attributes'][$key] = null;
                        }
                    }
                }
            }
            unset($event);
        }
        unset($span);

        return $spans;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    abstract protected function buildAttribute($key, $value);

    /**
     * @param $string
     *
     * @return bool
     */
    protected static function isBase64($string)
    {
        if (strlen($string) % 4 !== 0) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
            return false;
        }

        $decoded = base64_decode($string, true);
        if ($decoded === false) {
            return false; // @codeCoverageIgnore
        }

        return base64_encode($decoded) === $string;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    protected static function isList(array $array)
    {
        $expected = 0;
        foreach ($array as $key => $_) {
            if ($key === $expected) {
                $expected++;
                continue;
            }
            return false;
        }
        return true;
    }
}
