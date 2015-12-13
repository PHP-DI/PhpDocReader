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
    const TAG_RETURN = 'return';
    const TAG_PROPERTY = 'var';
    const TAG_PARAMETER = 'param';

    /**
     * Parser for use-statements.
     *
     * @var UseStatementParser $parser
     */
    private $parser;

    /**
     * List of types that do not exist as classes or interfaces.
     *
     * @var array $ignoredTypes
     */
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
     * Enable or disable throwing errors when PhpDoc Errors occur (when parsing annotations).
     *
     * @var bool $ignorePhpDocErrors
     */
    private $ignorePhpDocErrors;

    /**
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
     * @return null|string Type of the property (content of var annotation)
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
     * @return null|string Type of the property (content of var annotation)
     */
    public function getPropertyClass(ReflectionProperty $property)
    {
        return $this->parseTagType($property, self::TAG_PROPERTY);
    }

    /**
     * Parse the docblock of the property to get all classes declared in the var annotation.
     *
     * @param ReflectionProperty $property
     *
     * @return string[]
     * @throws InvalidClassException
     */
    public function getPropertyClasses(ReflectionProperty $property)
    {
        return $this->parseTagTypes($property, self::TAG_PROPERTY);
    }

    /**
     * Parse the docblock of the property to get the class of the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @throws AnnotationException
     * @return null|string Type of the property (content of var annotation)
     *
     * @deprecated Use getParameterClass instead.
     */
    public function getParameterType(ReflectionParameter $parameter)
    {
        return $this->getParameterClass($parameter);
    }

    /**
     * Parse the docblock of the method to get the class of the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @throws AnnotationException
     * @return null|string Type of the property (content of var annotation)
     */
    public function getParameterClass(ReflectionParameter $parameter)
    {
        return $this->parseTagType($parameter, self::TAG_PARAMETER);
    }

    /**
     * Parse the docblock of the method to get all classes declared in the param annotation.
     *
     * @param ReflectionParameter $parameter
     *
     * @return string[]
     * @throws InvalidClassException
     */
    public function getParameterClasses(ReflectionParameter $parameter)
    {
        return $this->parseTagTypes($parameter, self::TAG_PARAMETER);
    }

    /**
     * Parse the docblock of the method to get the class of the return annotation.
     *
     * @param ReflectionMethod $method
     *
     * @return null|string
     * @throws InvalidClassException
     */
    public function getMethodReturnClass(ReflectionMethod $method)
    {
        return $this->parseTagType($method, self::TAG_RETURN);
    }

    /**
     * Parse the docblock of the method to get all classes declared in the return annotation.
     *
     * @param ReflectionMethod $method
     *
     * @return string[]
     * @throws InvalidClassException
     */
    public function getMethodReturnClasses(ReflectionMethod $method)
    {
        return $this->parseTagTypes($method, self::TAG_RETURN);
    }

    /**
     * Detects and resolves the type declared by the specified $tagName on the provided $member.
     *
     * @param ReflectionProperty|ReflectionMethod|ReflectionParameter $member
     * @param string $tagName
     *
     * @return null|string Resolved type
     * @throws CannotResolveException
     * @throws InvalidClassException
     */
    private function parseTagType($member, $tagName)
    {
        $class = $member->getDeclaringClass();

        $typeDeclaration = $this->getTag($member, $tagName);

        $resolvedType = $this->resolveType($typeDeclaration, $class, $member);

        if ($resolvedType === null) {
            return null;
        }

        // Validate resolved type
        if (!$this->classExists($resolvedType)) {
            throw new InvalidClassException($resolvedType, $member);
        }

        // Strip preceding backslash to make a valid FQN
        return ltrim($resolvedType, '\\');
    }

    /**
     * Detects and resolves the types declared by the specified $tagName on the provided $member.
     *
     * @param ReflectionProperty|ReflectionMethod|ReflectionParameter $member
     * @param string $tagName
     *
     * @return string[] Array of resolved types
     * @throws CannotResolveException
     * @throws InvalidClassException
     */
    private function parseTagTypes($member, $tagName)
    {
        $class = $member->getDeclaringClass();

        $typeDeclaration = $this->getTag($member, $tagName);

        // Split up type declaration
        $types = explode('|', $typeDeclaration);

        $result = array();
        foreach ($types as $type)
        {
            $resolvedType = $this->resolveType($type, $class, $member);

            // Skip if null
            if ($resolvedType === null) {
                continue;
            }

            // Validate resolved type
            if (!$this->classExists($resolvedType)) {
                throw new InvalidClassException($resolvedType, $member);
            }

            // Strip preceding backslash to make a valid FQN
            $result[] = ltrim($resolvedType, '\\');
        }
        return $result;
    }

    /**
     * Retrieves the type declaration from the first tag of the specified $tagName that are relevant to the provided
     * $member.
     *
     * @param ReflectionProperty|ReflectionMethod|ReflectionParameter $member
     * @param string $tagName
     *
     * @return null|string Type declaration
     */
    private function getTag($member, $tagName)
    {
        $tags = $this->getTags($member, $tagName);

        return $tags ? $tags[0] : null;
    }

    /**
     * Retrieves type declarations from all tags of the specified $tagName that are relevant to the provided $member.
     *
     * @param ReflectionProperty|ReflectionMethod|ReflectionParameter $member
     * @param string $tagName
     *
     * @return string[] An array of type declarations
     */
    private function getTags($member, $tagName)
    {
        if ($member instanceof ReflectionParameter) {
            // Look for a tag for a specific variable
            $expression = '/@'.preg_quote($tagName).'\s+([^\s]+)\s+\$' . preg_quote($member->name) . '/';
            $docBlock = $member->getDeclaringFunction()->getDocComment();
        } else {
            // Generic tag search
            $expression = '/@'.preg_quote($tagName).'\s+([^\s]+)/';
            $docBlock = $member->getDocComment();
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
     * @throws CannotResolveException
     */
    private function resolveType($type, ReflectionClass $class, Reflector $member)
    {
        // Type hint?
        if ($member instanceof ReflectionParameter && $member->getClass() !== null) {
            return $member->getClass()->name;
        }

        if (!$this->isSupportedType($type)) {
            return null;
        }

        if ($this->isFullyQualified($type)) {
            return $type;
        }

        // Split alias from postfix
        list($alias, $postfix) = array_pad(explode('\\', $type, 2), 2, null);
        $alias = strtolower($alias);

        // Retrieve "use" statements
        $uses = $this->parser->parseUseStatements($class);

        // Imported class?
        if (array_key_exists($alias, $uses)) {
            return $postfix === null
                ? $uses[$alias]
                : $uses[$alias] . '\\' . $postfix;
        }

        // In class namespace?
        if ($this->classExists($class->getNamespaceName(). '\\' . $type)) {
            return $class->getNamespaceName() . '\\' . $type;
        }

        // No namespace?
        if ($this->classExists($type)) {
            return $type;
        }

        // Try resolving through related traits
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return $this->tryResolveFqnInTraits($type, $class, $member);
        }

        if ($this->ignorePhpDocErrors) {
            return null;
        }

        throw new CannotResolveException($type, $member);
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

        if ($this->ignorePhpDocErrors) {
            return null;
        }
        
        throw new CannotResolveException($type, $member);
    }

    /**
     * Determines whether the provided name is fully qualified.
     *
     * @param string $name
     *
     * @return bool Is the name fully qualified?
     */
    private function isFullyQualified($name)
    {
        return $name[0] === '\\';
    }

    /**
     * Determines whether the provided type is supported by the resolver.
     *
     * @param string $type
     *
     * @return bool Is the type supported?
     */
    private function isSupportedType($type)
    {
        return !in_array($type, $this->ignoredTypes)        // Exclude primitive types
            && preg_match('/^[a-zA-Z0-9\\\\_]+$/', $type);  // Exclude types containing special characters ([], <> ...)
    }

    /**
     * Determines whether the provided class exists as a class or interface.
     *
     * @param string $class
     *
     * @return bool
     */
    private function classExists($class)
    {
        return class_exists($class) || interface_exists($class);
    }
}
