<?php declare(strict_types=1);

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use UnitTest\PhpDocReader\FixturesPrimitiveTypes\Class1;

class PrimitiveTypesTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testProperties(string $type, string $expectedType)
    {
        $parser = new PhpDocReader;
        $class = new \ReflectionClass(Class1::class);

        $this->assertNull($parser->getPropertyClass($class->getProperty($type)));
        $this->assertEquals($expectedType, $parser->getPropertyType($class->getProperty($type)));
    }

    /**
     * @dataProvider typeProvider
     */
    public function testMethodParameters(string $type, string $expectedType)
    {
        $parser = new PhpDocReader;
        $parameter = new ReflectionParameter([Class1::class, 'foo'], $type);

        $this->assertNull($parser->getParameterClass($parameter));
        $this->assertEquals($expectedType, $parser->getParameterType($parameter));
    }

    public function typeProvider(): array
    {
        return [
            'bool' => ['bool', 'bool'],
            'boolean' => ['boolean', 'bool'],
            'string' => ['string', 'string'],
            'int' => ['int', 'int'],
            'integer' => ['integer', 'int'],
            'float' => ['float', 'float'],
            'double' => ['double', 'float'],
            'array' => ['array', 'array'],
            'object' => ['object', 'object'],
            'callable' => ['callable', 'callable'],
            'resource' => ['resource', 'resource'],
            'mixed' => ['mixed', 'mixed'],
            'iterable' => ['iterable', 'iterable'],
        ];
    }
}
