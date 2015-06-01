<?php

namespace UnitTest\PhpDocReader\Fixtures;

use UnitTest\PhpDocReader\Fixtures\Class3 as ClassClass3;

class Class1
{
    public $propNone;

    /**
     * @var \UnitTest\PhpDocReader\Fixtures\Class2
     */
    public $propFQN;

    /**
     * @var Class2
     */
    public $propLocalName;

    /**
     * @var ClassClass3
     */
    public $propAlias;

    /**
     * @param                                        $paramNone
     * @param                                        $paramTypeHint
     * @param \UnitTest\PhpDocReader\Fixtures\Class2 $paramFQN
     * @param Class2                                 $paramLocalName
     * @param ClassClass3                            $paramAlias
     */
    public function foo($paramNone, Class2 $paramTypeHint, $paramFQN, $paramLocalName, $paramAlias)
    {
    }
} 
