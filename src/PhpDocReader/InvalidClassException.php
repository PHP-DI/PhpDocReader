<?php

namespace PhpDocReader;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

class InvalidClassException extends AnnotationException
{
    const GENERIC_MESSAGE = 'The @%s annotation on %s::%s contains a non existent class "%s"';
    const PARAM_MESSAGE = 'The @param annotation for parameter "%s" of %s::%s contains a non existent class "%s"';

    public $type;

    public function __construct($type, Reflector $context)
    {
        parent::__construct();
        $this->type = $type;

        if ($context instanceof ReflectionProperty) {
            $this->message = sprintf(
                self::GENERIC_MESSAGE,
                'var',
                $context->getDeclaringClass()->name,
                $context->name,
                $type
            );
        } elseif ($context instanceof ReflectionMethod) {
            $this->message = sprintf(
                self::GENERIC_MESSAGE,
                'method',
                $context->getDeclaringClass()->name,
                $context->name,
                $type
            );
        } elseif ($context instanceof ReflectionParameter) {
            $this->message = sprintf(
                self::PARAM_MESSAGE,
                $context->name,
                $context->getDeclaringClass()->name,
                $context->getDeclaringFunction()->name,
                $type
            );
        }
    }
}
