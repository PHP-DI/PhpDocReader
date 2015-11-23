<?php

namespace UnitTest\PhpDocReader\FixturesReturnTag;

use UnitTest\PhpDocReader\FixturesReturnTag\DependencyClass1 as Foo;
use UnitTest\PhpDocReader\FixturesReturnTag\DependencyClass2 as Bar;

class Class1
{
    /**
     * @return Foo
     */
    public function singleReturnType()
    {

    }

    /**
     * @return Foo|Bar
     */
    public function multipleReturnType()
    {

    }
}
