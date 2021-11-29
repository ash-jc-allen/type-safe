<?php

namespace AshAllenDesign\TypeSafe\Tests\Unit;

use AshAllenDesign\TypeSafe\Exceptions\TypeSafeException;
use AshAllenDesign\TypeSafe\Tests\TestCase;
use AshAllenDesign\TypeSafe\Type;

class SafeTest extends TestCase
{
    /** @test */
    public function array_can_be_checked(): void
    {
        $prop = [];

        $result = safe($prop, Type::ARRAY);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function assoc_array_can_be_checked(): void
    {
        $prop = ['hello' => 'goodbye'];

        $result = safe($prop, Type::ASSOC_ARRAY);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function array_of_integers_can_be_checked(): void
    {
        $prop = [1, 2, 3];

        $result = safe($prop, Type::arrayOf(Type::INT));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function array_of_strings_can_be_checked(): void
    {
        $prop = ['1', '2', '3'];

        $result = safe($prop, Type::arrayOf(Type::STRING));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function array_of_objects_can_be_checked(): void
    {
        $prop = [new DummyClass(), new DummyClass(), new DummyClass()];

        $result = safe($prop, Type::arrayOf(DummyClass::class));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function assoc_array_of_strings_can_be_checked(): void
    {
        $prop = ['a' => 'hello', 'b' => 'goodbye', 'c' => 'hello'];

        $result = safe($prop, Type::assocArrayOf(Type::STRING, Type::STRING));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function assoc_array_of_strings_and_objects_can_be_checked(): void
    {
        $prop = ['a' => new DummyClass(), 'b' => new DummyClass(), 'c' => new DummyClass()];

        $result = safe(
            $prop,
            Type::assocArrayOf(Type::STRING, Type::object(DummyClass::class))
        );

        self::assertSame($prop, $result);
    }

    /** @test */
    public function object_can_be_checked(): void
    {
        $prop = new DummyClass();

        $result = safe($prop, Type::object(DummyClass::class));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function null_can_be_checked(): void
    {
        $prop = null;

        $result = safe(null, DummyClass::class);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function string_can_be_checked(): void
    {
        $prop = 'a';

        $result = safe($prop, Type::STRING);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function closure_can_be_checked(): void
    {
        $prop = function () {};

        $result = safe($prop, Type::CLOSURE);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function exception_is_thrown_if_the_parameter_is_a_string_but_an_integer_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);

        safe('a', Type::INT);
    }

    /** @test */
    public function exception_is_thrown_if_the_parameter_is_null_but_an_object_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);

        safe(null, Type::OBJECT);
    }

    /** @test */
    public function exception_is_thrown_if_the_array_is_of_strings_but_integers_were_expected(): void
    {
        $this->expectException(TypeSafeException::class);

        safe(['1', '2', '3'], Type::arrayOf(Type::INT));
    }

    /** @test */
    public function exception_is_thrown_if_the_array_is_of_strings_and_integers_but_only_integers_were_expected(): void
    {
        $this->expectException(TypeSafeException::class);

        safe([1, 2, '3'], Type::arrayOf(Type::INT));
    }

    /** @test */
    public function exception_is_thrown_if_the_array_passed_is_not_assoc_but_an_assoc_array_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);

        safe([1, 2, 3], Type::ASSOC_ARRAY);
    }
}
