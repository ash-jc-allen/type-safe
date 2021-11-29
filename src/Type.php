<?php

namespace AshAllenDesign\TypeSafe;

class Type
{
    public const STRING = 't_string';

    public const INT = 't_integer';

    public const DOUBLE = 't_double';

    public const ARRAY = 't_array';

    public const ASSOC_ARRAY = 't_assoc_array';

    public const CLOSURE = 't_closure';

    public const OBJECT = 't_object';

    public const BOOLEAN = 't_boolean';

    public static function arrayOf(mixed $valueType): string
    {
        return static::ARRAY.'_'.$valueType;
    }

    public static function assocArrayOf(mixed $keyType, mixed $valueType): string
    {
        return static::ASSOC_ARRAY.'_'.$keyType.','.$valueType;
    }

    public static function object(string $className): string
    {
        return static::OBJECT.'_'.$className;
    }
}
