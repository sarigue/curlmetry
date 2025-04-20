<?php

namespace Curlmetry\Test\Utils;

use Curlmetry\Utils\JsonUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonUtilsTest
 *
 * Tests the __construct and create methods of the JsonUtils class.
 */
class JsonUtilsTest extends TestCase
{
    public function testConstructWithValidJsonString()
    {
        $jsonString = '{"key": "value"}';
        $jsonUtils = new JsonUtils($jsonString);

        $this->assertEquals(['key' => 'value'], $jsonUtils->getJson());
    }

    public function testConstructWithInvalidJsonStringThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON - Error ');

        $invalidJsonString = '{"key": "value"';
        new JsonUtils($invalidJsonString);
    }

    public function testConstructWithArray()
    {
        $dataArray = ['key' => 'value'];
        $jsonUtils = new JsonUtils($dataArray);

        $this->assertEquals($dataArray, $jsonUtils->getJson());
    }

    public function testConstructWithObject()
    {
        $dataObject = (object)['key' => 'value'];
        $jsonUtils = new JsonUtils($dataObject);

        $this->assertEquals(['key' => 'value'], $jsonUtils->getJson());
    }

    public function testConstructWithNull()
    {
        $jsonUtils = new JsonUtils(null);

        $this->assertNull($jsonUtils->getJson());
    }

    public function testConstructWithUnsupportedType()
    {
        $unsupportedData = 12345;
        $jsonUtils = new JsonUtils($unsupportedData);

        $this->assertEquals($unsupportedData, $jsonUtils->getJson());
    }

    /**
     * Tests the create method with a valid JSON string.
     */
    public function testCreateWithValidJsonString()
    {
        $jsonString = '{"key": "value"}';
        $jsonUtils = JsonUtils::create($jsonString);

        $this->assertEquals(['key' => 'value'], $jsonUtils->getJson());
    }

    /**
     * Tests the create method with an invalid JSON string.
     */
    public function testCreateWithInvalidJsonStringThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON - Error ');

        $invalidJsonString = '{"key": "value"';
        JsonUtils::create($invalidJsonString);
    }

    /**
     * Tests the create method with an array.
     */
    public function testCreateWithArray()
    {
        $dataArray = ['key' => 'value'];
        $jsonUtils = JsonUtils::create($dataArray);

        $this->assertEquals($dataArray, $jsonUtils->getJson());
    }

    /**
     * Tests the create method with an object.
     */
    public function testCreateWithObject()
    {
        $dataObject = (object)['key' => 'value'];
        $jsonUtils = JsonUtils::create($dataObject);

        $this->assertIsArray($jsonUtils->getJson());
        $this->assertEquals(['key' => 'value'], $jsonUtils->getJson());
    }

    /**
     * Tests the create method with null.
     */
    public function testCreateWithNull()
    {
        $jsonUtils = JsonUtils::create(null);

        $this->assertNull($jsonUtils->getJson());
    }

    /**
     * Tests the create method with an unsupported type.
     */
    public function testCreateWithUnsupportedType()
    {
        $unsupportedData = 12345;
        $jsonUtils = JsonUtils::create($unsupportedData);

        $this->assertEquals($unsupportedData, $jsonUtils->getJson());
    }

    /**
     * Tests the getValue method with a valid key.
     */
    public function testGetValueWithValidKey()
    {
        $jsonUtils = new JsonUtils(['key' => 'value']);
        $this->assertEquals('value', $jsonUtils->getValue('key'));
    }

    /**
     * Tests the getValue method with a non-existent key and a default value.
     */
    public function testGetValueWithDefaultValue()
    {
        $jsonUtils = new JsonUtils(['key' => 'value']);
        $this->assertEquals('default', $jsonUtils->getValue('non_existent_key', 'default'));
    }

    /**
     * Tests the getValue method with a non-existent key and no default value.
     */
    public function testGetValueWithoutDefaultValue()
    {
        $jsonUtils = new JsonUtils(['key' => 'value']);
        $this->assertNull($jsonUtils->getValue('non_existent_key'));
    }
}
