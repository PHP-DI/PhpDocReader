<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use UnitTest\PhpDocReader\FixturesIssue335\Class3;

/**
 * @see https://github.com/PHP-DI/PHP-DI/issues/335
 */
class Issue335Test extends TestCase
{
    const CLASS_X = 'UnitTest\PhpDocReader\FixturesIssue335\ClassX';

    /**
     * This test ensures that namespaces are properly resolved for aliases that are defined in traits.
     * @see https://github.com/PHP-DI/PHP-DI/issues/335
     */
    public function testNamespaceResolutionForTraits()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Traits were introduced in PHP 5.4');
            return;
        }

        $parser = new PhpDocReader();

        $target = new Class3();

        $class = new ReflectionClass($target);

        $this->assertEquals(self::CLASS_X, $parser->getPropertyClass($class->getProperty("propTrait1")));
        $this->assertEquals(self::CLASS_X, $parser->getPropertyClass($class->getProperty("propTrait2")));
        
        $params = $class->getMethod("methodTrait1")->getParameters();
        $this->assertEquals(self::CLASS_X, $parser->getParameterClass($params[0]));
        
        $params = $class->getMethod("methodTrait2")->getParameters();
        $this->assertEquals(self::CLASS_X, $parser->getParameterClass($params[0]));
    }
}
