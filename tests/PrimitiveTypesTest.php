<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

/**
 * @see https://github.com/mnapoli/PhpDocReader/issues/1
 */
class PrimitiveTypesTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testProperties($type)
    {
        $parser = new PhpDocReader();
        $class = new \ReflectionClass('UnitTest\PhpDocReader\FixturesPrimitiveTypes\Class1');

        $this->assertNull($parser->getPropertyClass($class->getProperty($type)));
    }

    /**
     * @dataProvider typeProvider
     */
    public function testMethodParameters($type)
    {
        $parser = new PhpDocReader();
        $parameter = new ReflectionParameter(array('UnitTest\PhpDocReader\FixturesPrimitiveTypes\Class1', 'foo'), $type);

        $this->assertNull($parser->getParameterClass($parameter));
    }

    public function typeProvider()
    {
        return array(
            'bool'     => array('bool'),
            'boolean'  => array('boolean'),
            'string'   => array('string'),
            'int'      => array('int'),
            'integer'  => array('integer'),
            'float'    => array('float'),
            'double'   => array('double'),
            'array'    => array('array'),
            'object'   => array('object'),
            'callable' => array('callable'),
            'resource' => array('resource'),
            'mixed'    => array('mixed'),
        );
    }
}
