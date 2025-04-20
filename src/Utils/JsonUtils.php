<?php

namespace Curlmetry\Utils;

/**
 * A utility class for handling and processing JSON data.
 * Provides mechanisms to parse, validate, and retrieve data from JSON structures.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class JsonUtils
{
    private $json;

    public function __construct($data)
    {
        $json = null;
        if (is_string($data)) {
            $json = json_decode($data, true);
        } elseif (is_array($data)) {
            $json = $data;
        } elseif (is_object($data)) {
            $json = json_decode(json_encode($data), true);
        } else {
            $json = $data;
        }

        if ($json === null && $data !== null) {
            throw new \RuntimeException(
                'Invalid JSON - Error ' . json_last_error() . ' - ' . json_last_error_msg()
            );
        }

        $this->json = $json;
    }

    public static function create($data)
    {
        return new static($data);
    }

    /**
     * @return array|mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->json);
    }

    /***
     *
     * @param $key
     * @param $default_value
     *
     * @return mixed|null
     */
    public function getValue($key, $default_value = null)
    {
        if ($this->has($key)) {
            return $this->json[$key];
        }
        return $default_value;
    }
}
