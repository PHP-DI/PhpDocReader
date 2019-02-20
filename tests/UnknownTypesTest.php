<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use UnitTest\PhpDocReader\FixturesUnknownTypes\Class1;

/**
 * @see https://github.com/mnapoli/PhpDocReader/issues/3
 */
class UnknownTypesTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testProperties($type)
    {
        $parser = new PhpDocReader();
        $class = new \ReflectionClass(Class1::class);

        $this->assertNull($parser->getPropertyClass($class->getProperty($type)));
    }

    /**
     * @dataProvider typeProvider
     */
    public function testMethodParameters($type)
    {
        $parser = new PhpDocReader();
        $parameter = new ReflectionParameter([Class1::class, 'foo'], $type);

        $this->assertNull($parser->getParameterClass($parameter));
    }

    public function typeProvider()
    {
        return [
            'empty' => ['empty'],
            'array' => ['array'],
            'generics' => ['generics'],
            'multiple' => ['multiple'],
        ];
    }
}
