# PhpDocReader

![](https://img.shields.io/packagist/dt/PHP-DI/phpdoc-reader.svg)
[![CI](https://github.com/PHP-DI/PhpDocReader/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/PHP-DI/PhpDocReader/actions/workflows/ci.yml)

This project is used by:

- [PHP-DI 6](http://php-di.org/)
- [Woohoo Labs. Zen](https://github.com/woohoolabs/zen)

Fork the README to add your project here.

## Features

PhpDocReader parses `@var` and `@param` values in PHP docblocks:

```php

use My\Cache\Backend;

class Cache
{
    /**
     * @var Backend
     */
    protected $backend;

    /**
     * @param Backend $backend
     */
    public function __construct($backend)
    {
    }
}
```

It supports namespaced class names with the same resolution rules as PHP:

- fully qualified name (starting with `\`)
- imported class name (eg. `use My\Cache\Backend;`)
- relative class name (from the current namespace, like `SubNamespace\MyClass`)
- aliased class name  (eg. `use My\Cache\Backend as FooBar;`)

Primitive types (`@var string`) are ignored (returns null), only valid class names are returned.

## Usage

```php
$reader = new PhpDocReader();

// Read a property type (@var phpdoc)
$property = new ReflectionProperty($className, $propertyName);
$propertyClass = $reader->getPropertyClass($property);

// Read a parameter type (@param phpdoc)
$parameter = new ReflectionParameter([$className, $methodName], $parameterName);
$parameterClass = $reader->getParameterClass($parameter);
```
