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
     * @param T            $prop
     * @param Check|string $expectedType
     *
     * @throws InvalidTypeException
     * @throws TypeSafeException
     *
     * @return T
     */
    public function safe(mixed $prop, Check|string $expectedType): mixed
    {
        if (static::$skipChecks) {
            return $prop;
        }

        if ($expectedType instanceof Check) {
            return $this->runCustomCheck($prop, $expectedType);
        }

        return $this->runCheck($prop, $expectedType);
    }

    public static function skipChecks(bool $skipChecks = true): void
    {
        static::$skipChecks = $skipChecks;
    }

    /**
     * @param mixed $prop
     *
     * @throws TypeSafeException
     *
     * @return Closure
     */
    private function validateClosure(mixed $prop): Closure
    {
        if (!$prop instanceof \Closure) {
            $this->fail('The field is not a closure.');
        }

        return $prop;
    }

    /**
     * @param mixed       $prop
     * @param string|null $expectedType
     *
     * @throws InvalidTypeException
     * @throws TypeSafeException
     *
     * @return array
     */
    private function validateAssocArray(mixed $prop, ?string $expectedType): array
    {
        if (!is_array($prop)) {
            $this->fail('The field is not an array.');
        }

        if ($this->arrayIsList($prop)) {
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
     * @param mixed       $prop
     * @param string|null $expectedType
     *
     * @throws InvalidTypeException
     * @throws TypeSafeException
     *
     * @return array
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
     * @param mixed       $prop
     * @param string|null $expectedType
     *
     * @throws TypeSafeException
     *
     * @return object
     */
    private function validateObject(mixed $prop, ?string $expectedType): object
    {
        if (!is_object($prop)) {
            $this->fail('The field is not an object.');
        }

        if ($expectedType && !$prop instanceof $expectedType) {
            $this->fail('The field is not an instance of '.$expectedType);
        }

        return $prop;
    }

    /**
     * @param mixed  $prop
     * @param string $expectedType
     *
     * @throws InvalidTypeException
     * @throws TypeSafeException
     *
     * @return mixed
     */
    private function runCheck(mixed $prop, string $expectedType): mixed
    {
        if (strpos($expectedType, 't_') !== 0) {
            throw new InvalidTypeException($expectedType.' is not a valid type check.');
        }

        $exploded = explode(':', $expectedType);
        $type = $exploded[0];
        $specificType = $exploded[1] ?? null;

        return match ($type) {
            Type::CLOSURE     => $this->validateClosure($prop),
            Type::ARRAY       => $this->validateArray($prop, $specificType),
            Type::ASSOC_ARRAY => $this->validateAssocArray($prop, $specificType),
            Type::OBJECT      => $this->validateObject($prop, $specificType),
            default           => $this->validateField($prop, $expectedType),
        };
    }

    /**
     * @param mixed $prop
     * @param Check $expectedType
     *
     * @throws TypeSafeException
     *
     * @return mixed
     */
    private function runCustomCheck(mixed $prop, Check $expectedType): mixed
    {
        if (!$expectedType->passes($prop)) {
            $this->fail($expectedType->message($prop));
        }

        return $prop;
    }

    /**
     * @param mixed  $prop
     * @param string $expectedType
     *
     * @throws TypeSafeException
     *
     * @return mixed
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

    private function arrayIsList(array $array): bool
    {
        return $array === [] || (array_keys($array) === range(0, count($array) - 1));
    }
}
