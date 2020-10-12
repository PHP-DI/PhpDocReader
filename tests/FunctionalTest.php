<?php declare(strict_types=1);

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use ReflectionProperty;
use UnitTest\PhpDocReader\Fixtures\Class1;
use UnitTest\PhpDocReader\Fixtures\Class2;
use UnitTest\PhpDocReader\Fixtures\Class3;

class FunctionalTest extends TestCase
{
    public function testReadPropertyType()
    {
        $parser = new PhpDocReader;

        $className = Class1::class;

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propNone'));
        $this->assertNull($type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propFQN'));
        $this->assertEquals(Class2::class, $type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propLocalName'));
        $this->assertEquals(Class2::class, $type);

        $type = $parser->getPropertyClass(new ReflectionProperty($className, 'propAlias'));
        $this->assertEquals(Class3::class, $type);
    }

    public function testReadParamType()
    {
        $parser = new PhpDocReader;

        $method = [Class1::class, 'foo'];

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramNone'));
        $this->assertNull($type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramTypeHint'));
        $this->assertEquals(Class2::class, $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramFQN'));
        $this->assertEquals(Class2::class, $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramLocalName'));
        $this->assertEquals(Class2::class, $type);

        $type = $parser->getParameterClass(new ReflectionParameter($method, 'paramAlias'));
        $this->assertEquals(Class3::class, $type);
    }
}
