<?php

namespace AshAllenDesign\TypeSafe;

interface Check
{
    public function passes(mixed $prop): bool;

    public function message(mixed $prop): string;

}
