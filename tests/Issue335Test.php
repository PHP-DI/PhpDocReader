<?php

namespace UnitTest\PhpDocReader;

use PhpDocReader\PhpDocReader;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use UnitTest\PhpDocReader\FixturesIssue335\Class3;

/**
 * @see https://github.com/PHP-DI/PHP-DI/issues/335
 */
class Issue335Test extends PHPUnit_Framework_TestCase
{
    /**
     * This test ensures that namespaces are properly resolved for aliases that are defined in traits.
     * @see https://github.com/PHP-DI/PHP-DI/issues/335
     */
    public function testNamespaceResolutionForTraits()
    {
        $parser = new PhpDocReader();

        $target = new Class3();

        $class = new ReflectionClass($target);

        $this->assertEquals('UnitTest\PhpDocReader\FixturesIssue335\ClassX', $parser->getPropertyClass($class->getProperty("propTrait1")));
        $this->assertEquals('UnitTest\PhpDocReader\FixturesIssue335\ClassX', $parser->getPropertyClass($class->getProperty("propTrait2")));
        
        $params = $class->getMethod("methodTrait1")->getParameters();
        $this->assertEquals('UnitTest\PhpDocReader\FixturesIssue335\ClassX', $parser->getParameterClass($params[0]));
        
        $params = $class->getMethod("methodTrait2")->getParameters();
        $this->assertEquals('UnitTest\PhpDocReader\FixturesIssue335\ClassX', $parser->getParameterClass($params[0]));
    }
}
