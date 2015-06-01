<?php

namespace UnitTest\PhpDocReader\FixturesUnknownTypes;

class Class1
{
    /**
     * @var
     */
    public $empty;

    /**
     * @var Foo[]
     */
    public $array;

    /**
     * @var array<Foo>
     */
    public $generics;

    /**
     * @var Foo|Bar
     */
    public $multiple;

    /**
     * @param            $empty
     * @param Foo[]      $array
     * @param array<Foo> $generics
     * @param Foo|Bar    $multiple
     */
    public function foo(
        $empty,
        $array,
        $generics,
        $multiple
    ) {
    }
} 
