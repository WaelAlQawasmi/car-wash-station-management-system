<?php

namespace App\Core;

use RuntimeException;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private array $instances = [];

    public function set(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!class_exists($id)) {
            throw new RuntimeException("Class '$id' not found in container");
        }

        $reflector = new ReflectionClass($id);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            $instance = new $id();
        } else {
            $parameters = $constructor->getParameters();
            $dependencies = [];
            foreach ($parameters as $parameter) {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    $dependencies[] = $this->get($type->getName());
                } else {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                    } else {
                        throw new RuntimeException("Cannot resolve parameter '{$parameter->getName()}' in class '$id'");
                    }
                }
            }
            $instance = $reflector->newInstanceArgs($dependencies);
        }

        $this->instances[$id] = $instance;
        return $instance;
    }
}
