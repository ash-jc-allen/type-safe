<?php

namespace AshAllenDesign\TypeSafe;

class TypeSafe
{
    /**
     * @template T
     *
     * @param mixed $prop
     * @param T $expectedType
     * @return T
     * @throws \Exception
     */
    public static function safe(mixed $prop, mixed $expectedType): mixed
    {
        if ($expectedType instanceof Type) {
            return self::validateTypeEnum($prop, $expectedType);
        }

        $expectedTypeType = gettype($expectedType);

        if ($expectedTypeType === 'string') {
            return self::validateObject($prop, $expectedType);
        }

        if ($expectedTypeType === 'array') {
            // TODO Loop through all of the values.
            if (gettype(array_values($prop)[0]) !== gettype($expectedType[0])) {
                self::wrongType();
            }

            return $prop;
        }

        return $prop;
    }

    /**
     * @throws \Exception
     */
    private static function wrongType(): void
    {
        throw new \Exception('Wrong type');
    }

    /**
     * @template T
     *
     * @param string $type
     * @return T[]
     */
    public static function arrayOf(mixed $type): array
    {
        if ($type === Type::INT) {
            return [1];
        }

        if ($type === TYPE::STRING) {
            return ['a'];
        }

        return [];
    }

    public static function assocArrayOf(mixed $key, mixed $value): array
    {
        $x = match($key) {
            Type::INT => 1,
            Type::STRING => 'a',
        };

        $y = match($value) {
            Type::INT => 1,
            Type::STRING => 'a',
        };

        if ($key === Type::INT) {
            return [1];
        }

        if ($value === TYPE::STRING) {
            return ['a'];
        }

        return [];
    }

    private static function validateTypeEnum(mixed $prop, mixed $expectedType): mixed
    {
        if ($expectedType === Type::CLOSURE) {
            if (! $prop instanceof \Closure) {
                self::wrongType();
            }

            return $prop;
        }

        if (gettype($prop) !== $expectedType->value) {
            self::wrongType();
        }

        return $prop;
    }

    private static function validateObject(mixed $prop, mixed $expectedType)
    {
        if (class_exists($expectedType) && $prop::class !== $expectedType) {
            self::wrongType();
        }

        return $prop;
    }
}
