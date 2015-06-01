<?php

namespace UnitTest\PhpDocReader\FixturesIssue87;

use UnitTest\PhpDocReader\FixturesIssue87\Subspace\SomeDependencyFixture;

/**
 * Has a dependency locally aliased from another namespace
 */
class TargetFixture1
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
