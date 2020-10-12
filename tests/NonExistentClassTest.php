<?php declare(strict_types=1);

namespace UnitTest\PhpDocReader;

use PhpDocReader\AnnotationException;
use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use UnitTest\PhpDocReader\FixturesNonExistentClass\Class1;

/**
 * Test exceptions when a class doesn't exist.
 */
class NonExistentClassTest extends TestCase
{
    public function testProperties(): void
    {
        $parser = new PhpDocReader;
        $class = new \ReflectionClass(Class1::class);

        $this->expectException(AnnotationException::class);
        $this->expectDeprecationMessage('The @var annotation on UnitTest\PhpDocReader\FixturesNonExistentClass\Class1::prop contains a non existent class "Foo". Did you maybe forget to add a "use" statement for this annotation?');

        $parser->getPropertyClass($class->getProperty('prop'));
    }

    public function testPropertiesAndIgnoreErrors(): void
    {
        $parser = new PhpDocReader(true);
        $class = new \ReflectionClass(Class1::class);

        $this->assertNull($parser->getPropertyClass($class->getProperty('prop')));
    }

    public function testMethodParameters(): void
    {
        $parser = new PhpDocReader;
        $parameter = new ReflectionParameter([Class1::class, 'foo'], 'param');

        $this->expectException(AnnotationException::class);
        $this->expectDeprecationMessage('The @param annotation for parameter "param" of UnitTest\PhpDocReader\FixturesNonExistentClass\Class1::foo contains a non existent class "Foo". Did you maybe forget to add a "use" statement for this annotation?');

        $parser->getParameterClass($parameter);
    }

    public function testMethodParametersAndIgnoreErrors(): void
    {
        $parser = new PhpDocReader(true);
        $parameter = new ReflectionParameter([Class1::class, 'foo'], 'param');

        $this->assertNull($parser->getParameterClass($parameter));
    }
}
