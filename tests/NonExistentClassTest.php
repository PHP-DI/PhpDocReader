<?php declare(strict_types=1);

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use UnitTest\PhpDocReader\FixturesNonExistentClass\Class1;

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
        $parser = new PhpDocReader;
        $class = new \ReflectionClass(Class1::class);

        $parser->getPropertyClass($class->getProperty('prop'));
    }

    /**
     * @expectedException \PhpDocReader\AnnotationException
     * @expectedExceptionMessage The @param annotation for parameter "param" of UnitTest\PhpDocReader\FixturesNonExistentClass\Class1::foo contains a non existent class "Foo". Did you maybe forget to add a "use" statement for this annotation?
     */
    public function testMethodParameters()
    {
        $parser = new PhpDocReader;
        $parameter = new ReflectionParameter([Class1::class, 'foo'], 'param');

        $parser->getParameterClass($parameter);
    }
}
