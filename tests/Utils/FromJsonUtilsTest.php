<?php

namespace Curlmetry\Test\Utils;

use Curlmetry\Utils\FromJsonUtils;
use PHPUnit\Framework\TestCase;

class FromJsonUtilsTest extends TestCase
{
    /**
     * Test building an instance when all parameters are valid and the class has a fromJson method.
     */
    public function testBuildInstanceWithValidClassAndFromJsonMethod()
    {
        $classname = DummyClassWithFromJson::class;
        $data = ['key' => 'value'];

        $result = FromJsonUtils::buildInstance($classname, $data, DummyClassBase::class);

        $this->assertInstanceOf(DummyClassWithFromJson::class, $result);
        $this->assertSame('value', $result->getKey());
    }

    /**
     * Test building an instance when the class does not have a fromJson method.
     */
    public function testBuildInstanceWithoutFromJsonMethod()
    {
        $classname = DummyClassWithoutFromJson::class;
        $data = ['key' => 'value'];

        $result = FromJsonUtils::buildInstance($classname, $data, DummyClassBase::class);

        $this->assertInstanceOf(DummyClassWithoutFromJson::class, $result);
    }

    /**
     * Test building an instance when the class name is invalid.
     */
    public function testBuildInstanceWithInvalidClass()
    {
        $classname = 'InvalidClassName';
        $result = FromJsonUtils::buildInstance($classname, ['key' => 'value'], DummyClassBase::class);

        $this->assertNull($result);
    }

    /**
     * Test building an instance when the class is not a subclass of the specified parent class.
     */
    public function testBuildInstanceWithInvalidParentClass()
    {
        $classname = DummyClassWithFromJson::class;
        $result = FromJsonUtils::buildInstance($classname, ['key' => 'value'], \stdClass::class);

        $this->assertNull($result);
    }

    /**
     * Test building an instance when the class name is empty.
     */
    public function testBuildInstanceWithEmptyClassName()
    {
        $result = FromJsonUtils::buildInstance('', ['key' => 'value'], DummyClassBase::class);

        $this->assertNull($result);
    }

    /**
     * Test building an instance when no data is provided but the class is valid.
     */
    public function testBuildInstanceWithNoData()
    {
        $classname = DummyClassWithoutFromJson::class;

        $result = FromJsonUtils::buildInstance($classname, [], DummyClassBase::class);

        $this->assertInstanceOf(DummyClassWithoutFromJson::class, $result);
    }
}
