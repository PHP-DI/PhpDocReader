<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;

/**
 * Test exceptions when a class doesn't exist.
 */
class NonExistentClassTest extends TestCase
{
    /**
     * @expectedException \PhpDocReader\AnnotationException
     * @expectedExceptionMessage The @var annotation on UnitTest\PhpDocReader\FixturesNonExistentClass\Class1::prop contains a non existent class "Foo". Did you maybe forget to add a "use" statement for this annotation?
     */
    public function testProperties()
    {
        $parser = new PhpDocReader();
        $class = new \ReflectionClass('UnitTest\PhpDocReader\FixturesNonExistentClass\Class1');

        $parser->getPropertyClass($class->getProperty('prop'));
    }

    /**
     * @expectedException \PhpDocReader\AnnotationException
     * @expectedExceptionMessage The @param annotation for parameter "param" of UnitTest\PhpDocReader\FixturesNonExistentClass\Class1::foo contains a non existent class "Foo". Did you maybe forget to add a "use" statement for this annotation?
     */
    public function testMethodParameters()
    {
        $parser = new PhpDocReader();
        $parameter = new ReflectionParameter(array('UnitTest\PhpDocReader\FixturesNonExistentClass\Class1', 'foo'), 'param');

        $parser->getParameterClass($parameter);
    }
}
