<?php

namespace AshAllenDesign\TypeSafe\Tests\Unit;

use AshAllenDesign\TypeSafe\Exceptions\InvalidTypeException;
use AshAllenDesign\TypeSafe\Exceptions\TypeSafeException;
use AshAllenDesign\TypeSafe\Tests\TestCase;
use AshAllenDesign\TypeSafe\Type;
use AshAllenDesign\TypeSafe\TypeSafe;

class SafeTest extends TestCase
{
    /** @test */
    public function array_can_be_checked(): void
    {
        $prop = [];

        $result = TypeSafe::array($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function assoc_array_can_be_checked(): void
    {
        $prop = ['hello' => 'goodbye'];

        $result = TypeSafe::assocArray($prop);

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

        $result = safe($prop, Type::arrayOf(Type::object(DummyClass::class)));

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

        $result = TypeSafe::object($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function specific_object_can_be_checked(): void
    {
        $prop = new DummyClass();

        $result = TypeSafe::object($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function string_can_be_checked(): void
    {
        $prop = 'a';

        $result = TypeSafe::string($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function closure_can_be_checked(): void
    {
        $prop = function () {
        };

        $result = TypeSafe::closure($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function true_boolean_can_be_checked(): void
    {
        $prop = true;

        $result = TypeSafe::boolean($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function false_boolean_can_be_checked(): void
    {
        $prop = false;

        $result = TypeSafe::boolean($prop);

        self::assertSame($prop, $result);
    }

    /** @test */
    public function custom_check_can_be_used(): void
    {
        $prop = 'a';

        $result = safe($prop, (new CustomCheck()));

        self::assertSame($prop, $result);
    }

    /** @test */
    public function exception_is_thrown_if_the_parameter_is_a_string_but_an_array_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not an array');

        safe('a', Type::ARRAY);
    }

    /** @test */
    public function exception_is_thrown_if_the_parameter_is_a_string_but_an_assoc_array_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not an array');

        safe('a', Type::ASSOC_ARRAY);
    }

    /** @test */
    public function exception_is_thrown_if_the_parameter_is_a_string_but_an_integer_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not a integer');

        safe('a', Type::INT);
    }

    /** @test */
    public function exception_is_thrown_if_a_closure_was_expected_but_an_object_was_passed(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not a closure.');

        safe(new DummyClass(), Type::CLOSURE);
    }

    /** @test */
    public function exception_is_thrown_if_the_array_is_of_strings_but_integers_were_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not a integer');

        safe(['1', '2', '3'], Type::arrayOf(Type::INT));
    }

    /** @test */
    public function exception_is_thrown_if_the_array_is_of_strings_and_integers_but_only_integers_were_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not a integer');

        safe([1, 2, '3'], Type::arrayOf(Type::INT));
    }

    /** @test */
    public function exception_is_thrown_if_the_array_passed_is_not_assoc_but_an_assoc_array_was_expected(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The array is not associative.');

        safe([1, 2, 3], Type::ASSOC_ARRAY);
    }

    /** @test */
    public function exception_is_thrown_if_an_assoc_array_of_string_and_int_is_expected_but_string_and_string_is_passed(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not a integer');

        $prop = ['a' => 'hello', 'b' => 'goodbye', 'c' => 'hello'];

        safe($prop, Type::assocArrayOf(Type::STRING, Type::INT));
    }

    /** @test */
    public function exception_is_thrown_if_an_object_was_expected_but_a_string_was_passed(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not an object.');

        safe('hello', Type::OBJECT);
    }

    /** @test */
    public function exception_is_thrown_if_the_expected_object_is_different_to_the_object_passed(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The field is not an instance of '.DummyClass::class);

        safe(new AnotherClass(), Type::object(DummyClass::class));
    }

    /** @test */
    public function exception_is_thrown_if_the_type_check_passed_is_invalid(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('INVALID is not a valid type check.');

        safe(1, 'INVALID');
    }

    /** @test */
    public function checks_can_be_skipped(): void
    {
        TypeSafe::skipChecks(true);

        self::assertSame(
            1,
            safe(1, Type::STRING),
        );

        TypeSafe::skipChecks(false);
    }

    /** @test */
    public function exception_is_thrown_if_a_custom_check_fails(): void
    {
        $this->expectException(TypeSafeException::class);
        $this->expectExceptionMessage('The custom check field is not a string.');

        $prop = 1;

        safe($prop, (new CustomCheck()));
    }
}
