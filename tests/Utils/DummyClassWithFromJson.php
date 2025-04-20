<?php

namespace Curlmetry\Test\Utils;

class DummyClassWithFromJson extends DummyClassBase
{
    private $key;

    public static function fromJson(array $data)
    {
        $instance = new self();
        if (isset($data['key'])) {
            $instance->key = $data['key'];
        }
        return $instance;
    }

    public function getKey()
    {
        return $this->key;
    }
}
