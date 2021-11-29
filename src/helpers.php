<?php

use AshAllenDesign\TypeSafe\TypeSafe;

if (!function_exists('safe')) {
    function safe(mixed $prop, mixed $expectedType): mixed
    {
        return (new TypeSafe())->safe($prop, $expectedType);
    }
}
