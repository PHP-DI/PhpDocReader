<?php

namespace UnitTest\PhpDocReader\FixturesIssue335;

use UnitTest\PhpDocReader\FixturesIssue335\ClassX as Bar;
use UnitTest\PhpDocReader\FixturesIssue335\ClassX as MethodBar;

trait Trait2
{
    /**
     * @var Bar $propTrait2
     */
    protected $propTrait2;
    
    /**
     * @param MethodBar $parameter
     */
    public function methodTrait2($parameter)
    {

    }
}
