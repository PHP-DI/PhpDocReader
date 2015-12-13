<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use UnitTest\PhpDocReader\FixturesReturnTag\Class1;

/**
 * @see https://github.com/PHP-DI/PhpDocReader/issues/5
 */
class ReturnTagTest extends PHPUnit_Framework_TestCase
{
    const DP1 = 'UnitTest\PhpDocReader\FixturesReturnTag\DependencyClass1';
    const DP2 = 'UnitTest\PhpDocReader\FixturesReturnTag\DependencyClass2';

    /**
     * This test ensures that the return tag is properly returned
     * @see https://github.com/PHP-DI/PhpDocReader/issues/5
     */
    public function testGetReturnType()
    {
        $parser = new PhpDocReader();

        $target = new Class1();

        $class = new ReflectionClass($target);

        $this->assertEquals(self::DP1, $parser->getMethodReturnClass($class->getMethod("singleReturnType")));
        $this->assertEquals(null, $parser->getMethodReturnClass($class->getMethod("multipleReturnType")));
    }

    /**
     * This test ensures that the all types in the return tag are properly returned
     * @see https://github.com/PHP-DI/PhpDocReader/issues/5
     */
    public function testGetAllReturnTypes()
    {
        $parser = new PhpDocReader();

        $target = new Class1();

        $class = new ReflectionClass($target);

        $types = $parser->getMethodReturnClasses($class->getMethod("singleReturnType"));
        $this->assertCount(1, $types);
        $this->assertContains(self::DP1, $types);

        $types = $parser->getMethodReturnClasses($class->getMethod("multipleReturnType"));
        $this->assertCount(2, $types);
        $this->assertContains(self::DP1, $types);
        $this->assertContains(self::DP2, $types);
    }
}
