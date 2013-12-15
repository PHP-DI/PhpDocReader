<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use ReflectionParameter;

/**
 * @see https://github.com/mnapoli/PhpDocReader/issues/1
 */
class Issue1Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testProperties($type)
    {
        $parser = new PhpDocReader();
        $class = new \ReflectionClass('UnitTest\PhpDocReader\FixturesIssue1\Class1');

        $this->assertNull($parser->getPropertyClass($class->getProperty($type)));
    }

    /**
     * @dataProvider typeProvider
     */
    public function testMethodParameters($type)
    {
        $parser = new PhpDocReader();
        $parameter = new ReflectionParameter(array('UnitTest\PhpDocReader\FixturesIssue1\Class1', 'foo'), $type);

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
        );
    }
}
