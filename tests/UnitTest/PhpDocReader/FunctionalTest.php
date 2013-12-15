<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Functional tests
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testReadPropertyType()
    {
        $parser = new PhpDocReader();

        $className = 'UnitTest\PhpDocReader\Fixtures\Class1';

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propNone'));
        $this->assertNull($type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propFQN'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class2', $type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propLocalName'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class2', $type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propAlias'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class3', $type);
    }

    public function testReadParamType()
    {
        $parser = new PhpDocReader();

        $method = array('UnitTest\PhpDocReader\Fixtures\Class1', 'foo');

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramNone'));
        $this->assertNull($type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramTypeHint'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class2', $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramFQN'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class2', $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramLocalName'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class2', $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramAlias'));
        $this->assertEquals('UnitTest\PhpDocReader\Fixtures\Class3', $type);
    }
}
