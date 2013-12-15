<?php

namespace PhpDocReader;

use Doctrine\Common\Annotations\PhpParser;
use ReflectionParameter;
use ReflectionProperty;

/**
 * PhpDoc reader
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpDocReader
{
    /**
     * @var PhpParser
     */
    private $phpParser;

    private $ignoredTypes = array(
        'bool',
        'boolean',
        'string',
        'int',
        'integer',
        'float',
        'double',
        'array',
        'object',
        'callable',
        'resource',
    );

    public function __construct()
    {
        $this->phpParser = new PhpParser();
    }

    /**
     * Parse the docblock of the property to get the var annotation.
     *
     * @param ReflectionProperty $property
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     * @todo Rename to getPropertyClass
     */
    public function getPropertyType(ReflectionProperty $property)
    {
        // Get the content of the @var annotation
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            list(, $type) = $matches;
        } else {
            return null;
        }

        // Ignore primitive types
        if (in_array($type, $this->ignoredTypes)) {
            return null;
        }

        $class = $property->getDeclaringClass();

        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            $alias = (false === $pos = strpos($type, '\\')) ? $type : substr($type, 0, $pos);
            $loweredAlias = strtolower($alias);

            // Retrieve "use" statements
            $uses = $this->phpParser->parseClass($property->getDeclaringClass());

            $found = false;

            if (isset($uses[$loweredAlias])) {
                // Imported classes
                if (false !== $pos) {
                    $type = $uses[$loweredAlias] . substr($type, $pos);
                } else {
                    $type = $uses[$loweredAlias];
                }
                $found = true;
            } elseif ($this->classExists($class->getNamespaceName() . '\\' . $type)) {
                $type = $class->getNamespaceName() . '\\' . $type;
                $found = true;
            } elseif (isset($uses['__NAMESPACE__']) && $this->classExists($uses['__NAMESPACE__'] . '\\' . $type)) {
                // Class namespace
                $type = $uses['__NAMESPACE__'] . '\\' . $type;
                $found = true;
            } elseif ($this->classExists($type)) {
                // No namespace
                $found = true;
            }

            if (!$found) {
                throw new AnnotationException(sprintf(
                    'The @var annotation on %s::%s contains a non existent class "%s". '
                        . 'Did you maybe forget to add a "use" statement for this annotation?',
                    $type,
                    $class->name,
                    $property->getName()
                ));
            }
        }

        if (!$this->classExists($type)) {
            throw new AnnotationException(sprintf(
                'The @var annotation on %s::%s contains a non existent class "%s"',
                $type,
                $class->name,
                $property->getName()
            ));
        }

        // Remove the leading \ (FQN shouldn't contain it)
        $type = ltrim($type, '\\');

        return $type;
    }

    /**
     * Parse the docblock of the property to get the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     * @todo Rename to getParameterClass
     */
    public function getParameterType(ReflectionParameter $parameter)
    {
        // Use reflection
        $parameterClass = $parameter->getClass();
        if ($parameterClass !== null) {
            return $parameterClass->name;
        }

        $parameterName = $parameter->name;
        // Get the content of the @param annotation
        $method = $parameter->getDeclaringFunction();
        if (preg_match('/@param\s+([^\s]+)\s+\$' . $parameterName . '/', $method->getDocComment(), $matches)) {
            list(, $type) = $matches;
        } else {
            return null;
        }

        // Ignore primitive types
        if (in_array($type, $this->ignoredTypes)) {
            return null;
        }

        $class = $parameter->getDeclaringClass();

        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            $alias = (false === $pos = strpos($type, '\\')) ? $type : substr($type, 0, $pos);
            $loweredAlias = strtolower($alias);

            // Retrieve "use" statements
            $uses = $this->phpParser->parseClass($class);

            $found = false;

            if (isset($uses[$loweredAlias])) {
                // Imported classes
                if (false !== $pos) {
                    $type = $uses[$loweredAlias] . substr($type, $pos);
                } else {
                    $type = $uses[$loweredAlias];
                }
                $found = true;
            } elseif ($this->classExists($class->getNamespaceName() . '\\' . $type)) {
                $type = $class->getNamespaceName() . '\\' . $type;
                $found = true;
            } elseif (isset($uses['__NAMESPACE__']) && $this->classExists($uses['__NAMESPACE__'] . '\\' . $type)) {
                // Class namespace
                $type = $uses['__NAMESPACE__'] . '\\' . $type;
                $found = true;
            } elseif ($this->classExists($type)) {
                // No namespace
                $found = true;
            }

            if (!$found) {
                throw new AnnotationException(sprintf(
                    'The @param annotation for parameter %s of %s::%s contains a non existent class "%s". '
                        . 'Did you maybe forget to add a "use" statement for this annotation?',
                    $type,
                    $parameterName,
                    $class->name,
                    $method->name
                ));
            }
        }

        if (!$this->classExists($type)) {
            throw new AnnotationException(sprintf(
                'The @param annotation for parameter %s of %s::%s contains a non existent class "%s"',
                $type,
                $parameterName,
                $class->name,
                $method->name
            ));
        }

        // Remove the leading \ (FQN shouldn't contain it)
        $type = ltrim($type, '\\');

        return $type;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function classExists($class)
    {
        return class_exists($class) || interface_exists($class);
    }
}
