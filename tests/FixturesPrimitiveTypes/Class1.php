<?php

namespace UnitTest\PhpDocReader\FixturesPrimitiveTypes;

class Class1
{
    /**
     * @var bool
     */
    public $bool;

    /**
     * @var boolean
     */
    public $boolean;

    /**
     * @var string
     */
    public $string;

    /**
     * @var int
     */
    public $int;

    /**
     * @var integer
     */
    public $integer;

    /**
     * @var float
     */
    public $float;

    /**
     * @var double
     */
    public $double;

    /**
     * @var array
     */
    public $array;

    /**
     * @var object
     */
    public $object;

    /**
     * @var callable
     */
    public $callable;

    /**
     * @var resource
     */
    public $resource;

    /**
     * @var mixed
     */
    public $mixed;

    /**
     * @var iterable
     */
    public $iterable;

    /**
     * @param bool     $bool
     * @param boolean  $boolean
     * @param string   $string
     * @param int      $int
     * @param integer  $integer
     * @param float    $float
     * @param double   $double
     * @param array    $array
     * @param object   $object
     * @param callable $callable
     * @param resource $resource
     * @param mixed    $mixed
     * @param iterable $iterable
     */
    public function foo(
        $bool,
        $boolean,
        $string,
        $int,
        $integer,
        $float,
        $double,
        $array,
        $object,
        $callable,
        $resource,
        $mixed,
        $iterable
    ) {
    }
} 
