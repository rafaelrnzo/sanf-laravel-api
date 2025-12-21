<?php

namespace Sanf\Core\Traits;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Casts DTO properties typed as int or float when incoming payload provides strings.
 */
trait CastsNumericDtoProperties
{
    public function __construct(array $parameters = [])
    {
        $parameters = $this->castNumericDtoProperties($parameters);

        parent::__construct($parameters);
    }

    protected function castNumericDtoProperties(array $parameters): array
    {
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $typeName = $this->resolveNumericType($property);

            if (!$typeName) {
                continue;
            }

            $field = $property->getName();

            if (!array_key_exists($field, $parameters)) {
                continue;
            }

            $value = $parameters[$field];

            if (!is_scalar($value) || !is_numeric($value)) {
                continue;
            }

            $parameters[$field] = $typeName === 'int' ? (int) $value : (float) $value;
        }

        return $parameters;
    }

    protected function resolveNumericType(ReflectionProperty $property): ?string
    {
        $type = $property->getType();

        if ($type instanceof ReflectionNamedType) {
            return $this->typeNameIfNumeric($type);
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if (!$unionType instanceof ReflectionNamedType) {
                    continue;
                }

                $numericType = $this->typeNameIfNumeric($unionType);

                if ($numericType) {
                    return $numericType;
                }
            }
        }

        return null;
    }

    protected function typeNameIfNumeric(ReflectionNamedType $type): ?string
    {
        if (!$type->isBuiltin()) {
            return null;
        }

        $typeName = $type->getName();

        return in_array($typeName, ['int', 'float'], true) ? $typeName : null;
    }
}
