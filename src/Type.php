<?php

namespace AshAllenDesign\TypeSafe;

enum Type: string
{
    case STRING = 'string';

    case INT = 'integer';

    case DOUBLE = 'double';

    case ARRAY = 'array';

    case CLOSURE = 'closure';
}
