<?php

namespace UnitTest\PhpDocReader\FixturesIssue335;

use UnitTest\PhpDocReader\FixturesIssue335\ClassX as Foo;
use UnitTest\PhpDocReader\FixturesIssue335\ClassX as MethodFoo;

trait Trait1
{
    /**
     * @var Foo $propTrait1
     */
    protected $propTrait1;

    /**
     * @param MethodFoo $parameter
     */
    public function methodTrait1($parameter)
    {
        
    }
}
