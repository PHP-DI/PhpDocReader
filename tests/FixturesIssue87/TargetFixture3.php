<?php

namespace UnitTest\PhpDocReader\FixturesIssue87;

/**
 * Has a dependency in the local namespace
 */
class TargetFixture3
{

    /**
     * @var SomeDependencyFixture
     */
    protected $dependency1;

    /**
     * @var Subspace\SomeDependencyFixture2
     */
    protected $dependency2;

    /**
     * @param SomeDependencyFixture $dependency1
     * @param Subspace\SomeDependencyFixture2 $dependency2
     */
    public function SomeMethod($dependency1, $dependency2)
    {
    }

}
