<?php

namespace AshAllenDesign\TypeSafe\Tests\Unit;

use AshAllenDesign\TypeSafe\Check;

class CustomCheck implements Check
{
    public function passes(mixed $prop): bool
    {
        return is_string($prop);
    }

    public function message(mixed $prop): string
    {
        return 'The custom check field is not a string.';
    }
}
