<?php

namespace AshAllenDesign\TypeSafe;

use AshAllenDesign\TypeSafe\Exceptions\InvalidTypeException;
use AshAllenDesign\TypeSafe\Exceptions\TypeSafeException;
use Closure;

class TypeSafe
{
    private static bool $skipChecks = false;

    /**
     * @template T
     *
     * @param T $prop
     * @param string $expectedType
     * @return T
     * @throws TypeSafeException
     * @throws InvalidTypeException
     */
    public function safe(mixed $prop, string $expectedType): mixed
    {
        if (static::$skipChecks) {
            return $prop;
        }

        if (!str_starts_with($expectedType, 't_')) {
            throw new InvalidTypeException($expectedType.' is not a valid type check.');
        }

        $exploded = explode(':', $expectedType);
        $type = $exploded[0];
        $specificType = $exploded[1] ?? null;

        return match($type) {
            Type::CLOSURE => $this->validateClosure($prop),
            Type::ARRAY => $this->validateArray($prop, $specificType),
            Type::ASSOC_ARRAY => $this->validateAssocArray($prop, $specificType),
            Type::OBJECT => $this->validateObject($prop, $specificType),
            default => $this->validateField($prop, $expectedType),
        };
    }

    public static function skipChecks(bool $skipChecks = true): void
    {
        static::$skipChecks = $skipChecks;
    }

    /**
     * @param mixed $prop
     * @return Closure
     * @throws TypeSafeException
     */
    private function validateClosure(mixed $prop): Closure
    {
        if (!$prop instanceof \Closure) {
            $this->fail('The field is not a closure.');
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string|null $expectedType
     * @return array
     * @throws InvalidTypeException
     * @throws TypeSafeException
     */
    private function validateAssocArray(mixed $prop, ?string $expectedType): array
    {
        if (!is_array($prop)) {
            $this->fail('The field is not an array.');
        }

        if (array_is_list($prop)) {
            $this->fail('The array is not associative.');
        }

        // If we have explicitly defined the types that the keys
        // and values in the array should be, loop through it
        // and check the type of each item.
        if ($expectedType) {
            [$keyType, $valueType] = explode(',', $expectedType);

            foreach ($prop as $key => $value) {
                $this->safe($key, $keyType);
                $this->safe($value, $valueType);
            }
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string|null $expectedType
     * @return array
     * @throws InvalidTypeException
     * @throws TypeSafeException
     */
    private function validateArray(mixed $prop, ?string $expectedType): array
    {
        if (!is_array($prop)) {
            $this->fail('The field is not an array.');
        }

        // If we have explicitly defined the types that the values in
        // the array should be, loop through and check each one.
        if ($expectedType) {
            foreach ($prop as $value) {
                $this->safe($value, $expectedType);
            }
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string|null $expectedType
     * @return object
     * @throws TypeSafeException
     */
    private function validateObject(mixed $prop, ?string $expectedType): object
    {
        if (!is_object($prop)) {
            $this->fail('The field is not an object.');
        }

        if ($expectedType && !$prop instanceof $expectedType) {
            $this->fail('The field is not an instance of ' . $expectedType);
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string $expectedType
     * @return mixed
     * @throws TypeSafeException
     */
    private function validateField(mixed $prop, string $expectedType): mixed
    {
        $type = ltrim($expectedType, 't_');

        if (gettype($prop) !== $type) {
            $this->fail('The field is not a '.$type);
        }

        return $prop;
    }

    /**
     * @throws TypeSafeException
     */
    private function fail(string $message = ''): void
    {
        throw new TypeSafeException($message);
    }
}
