<?php declare(strict_types=1);

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use UnitTest\PhpDocReader\FixturesIssue335\Class3;
use UnitTest\PhpDocReader\FixturesIssue335\ClassX;

/**
 * @see https://github.com/PHP-DI/PHP-DI/issues/335
 */
class Issue335Test extends TestCase
{
    /**
     * This test ensures that namespaces are properly resolved for aliases that are defined in traits.
     *
     * @see https://github.com/PHP-DI/PHP-DI/issues/335
     */
    public function testNamespaceResolutionForTraits()
    {
        $parser = new PhpDocReader;

        $target = new Class3;

        $class = new ReflectionClass($target);

        $this->assertEquals(ClassX::class, $parser->getPropertyClass($class->getProperty('propTrait1')));
        $this->assertEquals(ClassX::class, $parser->getPropertyClass($class->getProperty('propTrait2')));

        $params = $class->getMethod('methodTrait1')->getParameters();
        $this->assertEquals(ClassX::class, $parser->getParameterClass($params[0]));

        $params = $class->getMethod('methodTrait2')->getParameters();
        $this->assertEquals(ClassX::class, $parser->getParameterClass($params[0]));
    }
}
