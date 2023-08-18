<?php
namespace Tests;

use Src\Migrations\CollectionsMigration;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

require __DIR__ . '/../vendor/autoload.php';

CollectionsMigration::run();

function getPrivateMethod(string $className, string $methodName): ReflectionMethod
{
    $class = new ReflectionClass($className);
    $method = $class->getMethod($methodName);
    $method->setAccessible(true);

    return $method;
}

function getPrivateProperty(string $className, string $propertyName): ReflectionProperty
{
    $class = new ReflectionClass($className);
    $property = $class->getProperty($propertyName);
    $property->setAccessible(true);

    return $property;
}