<?php

namespace AshAllenDesign\TypeSafe;

use AshAllenDesign\TypeSafe\Exceptions\TypeSafeException;

class TypeSafe
{
    /**
     * @template T
     *
     * @param mixed $prop
     * @param T $expectedType
     * @return T
     * @throws TypeSafeException
     */
    public function safe(mixed $prop, mixed $expectedType): mixed
    {
        // Our own type checks.
        if (str_starts_with($expectedType, 't_')) {
            // Closure checks.
            if ($expectedType === Type::CLOSURE) {
                if (!$prop instanceof \Closure) {
                    self::wrongType();
                }

                return $prop;
            }

            if (str_starts_with($expectedType, Type::ASSOC_ARRAY)) {
                if (array_is_list($prop)) {
                    self::wrongType();
                }

                $assocTypes = $this->trimFromStart($expectedType, Type::ASSOC_ARRAY);

                if ($assocTypes !== '') {
                    [$keyType, $valueType] = explode(',', $this->trimFromStart($assocTypes, '_'));

                    foreach ($prop as $key => $value) {
                        $this->safe($key, $keyType);
                        $this->safe($value, $valueType);
                    }
                }

                return $prop;
            }

            if (str_starts_with($expectedType, Type::ARRAY)) {
                if (! array_is_list($prop)) {
                    self::wrongType();
                }

                $type = $this->trimFromStart($expectedType, Type::ARRAY);


                if ($type !== '') {
                    foreach ($prop as $value) {
                        $this->safe($value, $this->trimFromStart($type, '_'));
                    }
                }

                return $prop;
            }

            // Object checks.
            if (str_starts_with($expectedType, 't_object_')) {
                $type = $this->trimFromStart($expectedType, 't_object_');

                if (!is_object($prop)) {
                    self::wrongType();
                }

                if (!$prop instanceof $type) {
                    self::wrongType();
                }

                return $prop;
            }

            // General types checks.
            if (gettype($prop) !== $this->trimFromStart($expectedType, 't_')) {
                self::wrongType();
            }

            return $prop;
        }

        // Null checks.
        if ($expectedType === null && $prop !== null) {
            self::wrongType();
        }

        return $prop;
    }

    /**
     * @throws TypeSafeException
     */
    private static function wrongType(): void
    {
        throw new TypeSafeException('Wrong type');
    }

    private function trimFromStart($string, $toRemove): string
    {
        if (str_starts_with($string, $toRemove)) {
            $string = substr($string, strlen($toRemove));
        }

        return $string;
    }
}
