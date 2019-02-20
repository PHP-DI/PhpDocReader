<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

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
        $class = new \ReflectionClass('UnitTest\PhpDocReader\FixturesUnknownTypes\Class1');

        $this->assertNull($parser->getPropertyClass($class->getProperty($type)));
    }

    /**
     * @dataProvider typeProvider
     */
    public function testMethodParameters($type)
    {
        $parser = new PhpDocReader();
        $parameter = new ReflectionParameter(array('UnitTest\PhpDocReader\FixturesUnknownTypes\Class1', 'foo'), $type);

        $this->assertNull($parser->getParameterClass($parameter));
    }

    public function typeProvider()
    {
        return array(
            'empty'    => array('empty'),
            'array'    => array('array'),
            'generics' => array('generics'),
            'multiple' => array('multiple'),
        );
    }
}
