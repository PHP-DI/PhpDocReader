# PhpDocReader

[![Build Status](https://travis-ci.org/mnapoli/PhpDocReader.png?branch=master)](https://travis-ci.org/mnapoli/PhpDocReader) [![Coverage Status](https://coveralls.io/repos/mnapoli/PhpDocReader/badge.png)](https://coveralls.io/r/mnapoli/PhpDocReader)

This project is a sub-project of [PHP-DI](http://mnapoli.github.io/PHP-DI/).

## Usage

```php
$reader = new PhpDocReader();

// Read a property type (@var phpdoc)
$property = new ReflectionProperty($className, $propertyName);
$propertyType = $reader->getPropertyType($property);

// Read a parameter type (@param phpdoc)
$parameter = new ReflectionParameter(array($className, $methodName), $parameterName);
$parameterType = $reader->getParameterType($parameter);
```
