<?php

namespace AshAllenDesign\TypeSafe;

use AshAllenDesign\TypeSafe\Exceptions\InvalidTypeException;
use AshAllenDesign\TypeSafe\Exceptions\TypeSafeException;
use Closure;
use Exception;

class TypeSafe
{
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
        if (!str_starts_with($expectedType, 't_')) {
            throw new InvalidTypeException($expectedType.' is not a valid type check.');
        }

        // Closure checks.
        if (str_starts_with($expectedType, Type::CLOSURE)) {
            return $this->validateClosure($prop);
        }

        // Assoc array checks.
        if (str_starts_with($expectedType, Type::ASSOC_ARRAY)) {
            return $this->validateAssocArray($prop, $expectedType);
        }

        // Array checks.
        if (str_starts_with($expectedType, Type::ARRAY)) {
            return $this->validateArray($prop, $expectedType);
        }

        // Object checks.
        if (str_starts_with($expectedType, Type::OBJECT)) {
            return $this->validateObject($prop, $expectedType);
        }

        // General types checks.
        return $this->validateField($prop, $expectedType);
    }

    /**
     * @throws TypeSafeException
     */
    // TODO Remove static.
    // TODO Rename to fail().
    private static function wrongType(string $message = ''): void
    {
        throw new TypeSafeException($message);
    }

    private function trimFromStart(string $string, string $toRemove): string
    {
        if (str_starts_with($string, $toRemove)) {
            $string = substr($string, strlen($toRemove));
        }

        return $string;
    }

    /**
     * @param mixed $prop
     * @return Closure
     * @throws TypeSafeException
     */
    private function validateClosure(mixed $prop): Closure
    {
        if (!$prop instanceof \Closure) {
            self::wrongType('The field is not a closure.');
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string $expectedType
     * @return array
     * @throws TypeSafeException
     * @throws InvalidTypeException
     */
    private function validateAssocArray(mixed $prop, string $expectedType): array
    {
        if (!is_array($prop)) {
            self::wrongType('The field is not an array.');
        }

        if (array_is_list($prop)) {
            self::wrongType('The array is not associative.');
        }

        $assocTypes = $this->trimFromStart($expectedType, Type::ASSOC_ARRAY);

        // If we have explicitly defined the types that the keys
        // and values in the array should be, loop through it
        // and check the type of each item.
        if ($assocTypes !== '') {
            [$keyType, $valueType] = explode(',', $this->trimFromStart($assocTypes, '_'));

            foreach ($prop as $key => $value) {
                $this->safe($key, $keyType);
                $this->safe($value, $valueType);
            }
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string $expectedType
     * @return array
     * @throws InvalidTypeException
     * @throws TypeSafeException
     */
    private function validateArray(mixed $prop, string $expectedType): array
    {
        if (!is_array($prop)) {
            self::wrongType('The field is not an array.');
        }

        $type = $this->trimFromStart($expectedType, Type::ARRAY);

        // If we have explicitly defined the types that the values in
        // the array should be, loop through and check each one.
        if ($type !== '') {
            foreach ($prop as $value) {
                $this->safe($value, $this->trimFromStart($type, '_'));
            }
        }

        return $prop;
    }

    /**
     * @param mixed $prop
     * @param string $expectedType
     * @return object
     * @throws TypeSafeException
     */
    private function validateObject(mixed $prop, string $expectedType): object
    {
        $type = $this->trimFromStart($expectedType, Type::OBJECT);

        if (!is_object($prop)) {
            self::wrongType('The field is not an object.');
        }

        if ($type !== '') {
            $type = $this->trimFromStart($type, '_');

            if (!$prop instanceof $type) {
                self::wrongType('The field is not an instance of ' . $type);
            }
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
        $type = $this->trimFromStart($expectedType, 't_');

        if (gettype($prop) !== $type) {
            self::wrongType('The field is not a '.$type);
        }

        return $prop;
    }
}
