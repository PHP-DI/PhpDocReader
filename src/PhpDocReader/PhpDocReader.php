<?php

namespace PhpDocReader;

use PhpDocReader\PhpParser\UseStatementParser;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * PhpDoc reader
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpDocReader
{
    /**
     * @var UseStatementParser
     */
    private $parser;

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

    /**
     * Enable or disable throwing errors when PhpDoc Errors occur (when parsing annotations)
     * 
     * @var bool
     */
    private $ignorePhpDocErrors;

    /**
     * 
     * @param bool $ignorePhpDocErrors
     */
    public function __construct($ignorePhpDocErrors = false)
    {
        $this->parser = new UseStatementParser();
        $this->ignorePhpDocErrors = $ignorePhpDocErrors;
    }

    /**
     * Parse the docblock of the property to get the class of the var annotation.
     *
     * @param ReflectionProperty $property
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     *
     * @deprecated Use getPropertyClass instead.
     */
    public function getPropertyType(ReflectionProperty $property)
    {
        return $this->getPropertyClass($property);
    }

    /**
     * Parse the docblock of the property to get the class of the var annotation.
     *
     * @param ReflectionProperty $property
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     */
    public function getPropertyClass(ReflectionProperty $property)
    {
        // Get the content of the @var annotation
        $type = $this->getTag('var', $property->getDocComment());

        // Ignore primitive types
        if (in_array($type, $this->ignoredTypes)) {
            return null;
        }

        // Ignore types containing special characters ([], <> ...)
        if (! preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type)) {
            return null;
        }

        $class = $property->getDeclaringClass();

        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            // Try to resolve the FQN using the class context
            $resolvedType = $this->resolveType($type, $class, $property);

            if (!$resolvedType && !$this->ignorePhpDocErrors) {
                throw new AnnotationException(sprintf(
                    'The @var annotation on %s::%s contains a non existent class "%s". '
                        . 'Did you maybe forget to add a "use" statement for this annotation?',
                    $class->name,
                    $property->getName(),
                    $type
                ));
            }
            
            $type = $resolvedType;
        }

        if (!$this->classExists($type) && !$this->ignorePhpDocErrors) {
            throw new AnnotationException(sprintf(
                'The @var annotation on %s::%s contains a non existent class "%s"',
                $class->name,
                $property->getName(),
                $type
            ));
        }

        // Remove the leading \ (FQN shouldn't contain it)
        $type = ltrim($type, '\\');

        return $type;
    }

    /**
     * Parse the docblock of the property to get the class of the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     *
     * @deprecated Use getParameterClass instead.
     */
    public function getParameterType(ReflectionParameter $parameter)
    {
        return $this->getParameterClass($parameter);
    }

    /**
     * Parse the docblock of the property to get the class of the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @throws AnnotationException
     * @return string|null Type of the property (content of var annotation)
     */
    public function getParameterClass(ReflectionParameter $parameter)
    {
        // Use reflection
        $parameterClass = $parameter->getClass();
        if ($parameterClass !== null) {
            return $parameterClass->name;
        }

        $parameterName = $parameter->name;
        // Get the content of the @param annotation
        $method = $parameter->getDeclaringFunction();
        $type = $this->getTag('param', $method->getDocComment(), $parameterName);

        // Ignore primitive types
        if (in_array($type, $this->ignoredTypes)) {
            return null;
        }

        // Ignore types containing special characters ([], <> ...)
        if (! preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type)) {
            return null;
        }

        $class = $parameter->getDeclaringClass();

        // If the class name is not fully qualified (i.e. doesn't start with a \)
        if ($type[0] !== '\\') {
            // Try to resolve the FQN using the class context
            $resolvedType = $this->resolveType($type, $class, $parameter);
         
            if (!$resolvedType && !$this->ignorePhpDocErrors) {
                throw new AnnotationException(sprintf(
                    'The @param annotation for parameter "%s" of %s::%s contains a non existent class "%s". '
                        . 'Did you maybe forget to add a "use" statement for this annotation?',
                    $parameterName,
                    $class->name,
                    $method->name,
                    $type
                ));
            }
            
            $type = $resolvedType;
        }

        if (!$this->classExists($type) && !$this->ignorePhpDocErrors) {
            throw new AnnotationException(sprintf(
                'The @param annotation for parameter "%s" of %s::%s contains a non existent class "%s"',
                $parameterName,
                $class->name,
                $method->name,
                $type
            ));
        }

        // Remove the leading \ (FQN shouldn't contain it)
        $type = ltrim($type, '\\');

        return $type;
    }

    public function getMethodReturnClass(ReflectionMethod $method)
    {/*
        // Get the content of the @var annotation
        $type = $this->getTag('return', $method->getDocComment());

        if (($pos = strpos($type, '|')) !== false) {
            $type = substr($type, 0, $pos);
        }

        $class = $method->getDeclaringClass();
        try {
            $type = $this->resolveType($type, $class, $method);

            if (!$this->classExists($type) && !$this->ignorePhpDocErrors) {
                throw new AnnotationException(sprintf(
                    'The @return annotation on %s::%s contains a non existent class "%s"',
                    $class->name,
                    $method->getName(),
                    $type
                ));
            }

            // Remove the leading \ (FQN shouldn't contain it)
            $type = ltrim($type, '\\');

            return $type;

        } catch (CannotResolveException $e) {
            if ($this->ignorePhpDocErrors) {
                return null;
            }
            throw new AnnotationException(sprintf(
                'The @return annotation on %s::%s contains a non existent class "%s". '
                . 'Did you maybe forget to add a "use" statement for this annotation?',
                $class->name,
                $method->getName(),
                $type
            ));
        }*/
    }

    public function getMethodReturnClasses(ReflectionMethod $method)
    {

    }

    /**
     * @param string $tagName
     * @param string $docBlock
     * @param string|null $variableName
     *
     * @return string
     */
    private function getTag($tagName, $docBlock, $variableName = null)
    {
        $tags = $this->getTags($tagName, $docBlock, $variableName);
        return $tags ? $tags[0] : null;
    }

    /**
     * @param string $tagName
     * @param string $docBlock
     * @param string|null $variableName
     *
     * @return string[]
     */
    private function getTags($tagName, $docBlock, $variableName = null)
    {
        if ($variableName === null) {
            // Generic tag search
            $expression = '/@' . preg_quote($tagName) . '\s+([^\s]+)/';
        } else {
            // Look for a tag for a specific variable
            $expression = '/@' . preg_quote($tagName) . '\s+([^\s]+)\s+\$' . preg_quote($variableName) . '/';
        }
        return preg_match_all($expression, $docBlock, $matches) ? $matches[1] : null;
    }

    /**
     * Attempts to resolve the FQN of the provided $type based on the $class and $member context.
     *
     * @param string $type
     * @param ReflectionClass $class
     * @param Reflector $member
     *
     * @return null|string Fully qualified name of the type, or null if it could not be resolved
     *
     * @throws CannotResolveException
     */
    private function resolveType($type, ReflectionClass $class, Reflector $member)
    {
        // Ignore primitive types
        if (in_array($type, $this->ignoredTypes)) {
            return null;
        }

        // Ignore types containing special characters ([], <> ...)
        if (! preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type)) {
            return null;
        }

        // Return if the class name is already fully qualified (i.e. starts with a \)
        if ($type[0] === '\\') {
            return $type;
        }

        $alias = ($pos = strpos($type, '\\')) === false ? $type : substr($type, 0, $pos);
        $loweredAlias = strtolower($alias);

        // Retrieve "use" statements
        $uses = $this->parser->parseUseStatements($class);

        if (isset($uses[$loweredAlias])) {
            // Imported classes
            if ($pos !== false) {
                return $uses[$loweredAlias] . substr($type, $pos);
            } else {
                return $uses[$loweredAlias];
            }
        } elseif ($this->classExists($class->getNamespaceName() . '\\' . $type)) {
            return $class->getNamespaceName() . '\\' . $type;

        // What is the scenario for this statement? This block is not hit in the unit tests.
        /*} elseif (isset($uses['__NAMESPACE__']) && $this->classExists($uses['__NAMESPACE__'] . '\\' . $type)) {
            // Class namespace
            return $uses['__NAMESPACE__'] . '\\' . $type;*/

        } elseif ($this->classExists($type)) {
            // No namespace
            return $type;
        }

        if (version_compare(phpversion(), '5.4.0', '<')) {
            throw new CannotResolveException($type);
        } else {
            // If all fail, try resolving through related traits
            return null;
            return $this->tryResolveFqnInTraits($type, $class, $member);
        }
    }

    /**
     * Attempts to resolve the FQN of the provided $type based on the $class and $member context, specifically searching
     * through the traits that are used by the provided $class.
     *
     * @param string $type
     * @param ReflectionClass $class
     * @param Reflector $member
     *
     * @return null|string Fully qualified name of the type, or null if it could not be resolved
     *
     * @throws CannotResolveException
     */
    private function tryResolveFqnInTraits($type, ReflectionClass $class, Reflector $member)
    {
        /** @var ReflectionClass[] $traits */
        $traits = array();

        // Get traits for the class and its parents
        while ($class) {
            $traits = array_merge($traits, $class->getTraits());
            $class = $class->getParentClass();
        }
        
        foreach ($traits as $trait) {
            // Eliminate traits that don't have the property/method/parameter
            if ($member instanceof ReflectionProperty && !$trait->hasProperty($member->name)) {
                continue;
            } elseif ($member instanceof ReflectionMethod && !$trait->hasMethod($member->name)) {
                continue;
            } elseif ($member instanceof ReflectionParameter && !$trait->hasMethod($member->getDeclaringFunction()->name)) {
                continue;
            }

            // Run the resolver again with the ReflectionClass instance for the trait
            $resolvedType = $this->resolveType($type, $trait, $member);
            
            if ($resolvedType) {
                return $resolvedType;
            }
        }

        throw new CannotResolveException($type);
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

class CannotResolveException extends \Exception
{
    public $type;

    public function __construct($type)
    {
        parent::__construct();
        $this->type = $type;
    }
}
