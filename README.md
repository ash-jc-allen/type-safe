<p align="center">
<img src="/docs/logo.png" alt="PHP Type Safe logo" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/ashallendesign/type-safe"><img src="https://img.shields.io/packagist/v/ashallendesign/type-safe.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://github.com/ash-jc-allen/type-safe"><img src="https://img.shields.io/github/workflow/status/ash-jc-allen/type-safe/run-tests?style=flat-square" alt="Build Status"></a>
<a href="https://packagist.org/packages/ashallendesign/type-safe"><img src="https://img.shields.io/packagist/dt/ashallendesign/type-safe.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ashallendesign/type-safe"><img src="https://img.shields.io/packagist/php-v/ashallendesign/type-safe?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/ash-jc-allen/short-url/blob/master/LICENSE"><img src="https://img.shields.io/github/license/ash-jc-allen/type-safe?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Usage](#usage)
    - [Simple Checks](#simple-checks)
    - [Advanced Checks](#advanced-checks)
    - [Custom Checks](#custom-checks)
    - [Skipping Checks](#skipping-checks)
- [Testing](#testing)
- [Security](#security)
- [Contribution](#contribution)
- [Credits](#credits)
- [Changelog](#changelog)
- [License](#license)

## Overview

Type Safe is a lightweight package that you can use in your PHP projects to ensure your variables' types.

## Installation

You can install the package via Composer:

```bash
composer require ashallendesign/type-safe
```

The package has been developed and tested to work with the following minimum requirements:

- PHP 8.0

## Usage

### Simple Checks

Validating that a property is an integer:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::INT);
```

Validating that a property is a string:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::STRING);
```

Validating that a property is a boolean:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::BOOLEAN);
```

Validating that a property is a closure:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::CLOSURE);
```

Validating that a property is an object:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::OBJECT);
```

Validating that a property is an array:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::ARRAY);
```

Validating that a property is an associative array:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::ASSOC_ARRAY);
```

### Advanced Checks

Validating that a property is an object of a specific class:

```php
use App\Models\User;
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::object(User::class));
```

Validating that a property is an array containing specific fields:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::arrayOf(Type::INT));
```

Validating that a property is an associative array containing specific fields:

```php
use AshAllenDesign\TypeSafe\Type;

$validatedField = safe($field, Type::assocArrayOf(Type::STRING, Type::STRING));
```

### Custom Checks

You might want to use your own custom checks that aren't provided in the package by default. To do this, you can create your own class that implements the `AshAllenDesign\TypeSafe\Check` interface.

The interface enforces two methods: `passes()` and `message()`. The `passes()` method is used to define your logic that determines if the field is the correct type. The `message()` method is used to return the message that will be passed to the thrown exception if the validation fails.

For example, if we wanted to create a custom check to assert that our field was a Laravel `Collection` that only contained `User` models, it might look something like this:

```php
use App\Models\User;
use AshAllenDesign\TypeSafe\Check;
use Illuminate\Support\Collection;

class LaravelUserCollection implements Check
{
    public function passes(mixed $prop): bool
    {
        if (!$prop instanceof Collection) {
            return false;
        }

        return $prop->whereInstanceOf(User::class)->count() === $prop->count();
    }

    public function message(mixed $prop): string
    {
        return 'One of the items is not a User model.';
    }
}
```

We could then use that check like so:

```php
$collection = collect([new User(), new TestCase()]);

safe($collection, new LaravelUserCollection());
````

### Skipping Checks

There may be times when you don't want to run the type checks. For example, you might want to disable them in production environments and only run them in local, testing and staging environments. To skip the checks, you can simply use the `skipChecks` like shown in the example below:

```php
use AshAllenDesign\TypeSafe\Type;
use AshAllenDesign\TypeSafe\TypeSafe;

TypeSafe::skipChecks();

$validatedField = safe($field, Type::ASSOC_ARRAY);
```

### Helpers Methods

There are three different ways that you can use the package to add type safe checks to your code.

The first method is by using the `TypeSafe` object itself like so:

```php
use AshAllenDesign\TypeSafe\TypeSafe;

$validatedField = (new TypeSafe())->safe($field, Type::INT);
```

Alternatively, you can use the `safe()` helper function that achieves the same thing as the code above. You can use the helper function like so:

```php
$validatedField = safe($field, Type::INT);
```

The `TypeSafe` also includes helper methods that you can use for all the simple checks. The example shows how you can validate an integer field:

```php
use AshAllenDesign\TypeSafe\TypeSafe;

$validatedField = TypeSafe::int($field);
```

## Testing

To run the tests for the package, you can use the following command:

```bash
composer test
```

## Security

If you find any security related issues, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

To contribute to this library, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
  pass and write more if needed.
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- Make all pull requests to the ``` master ``` branch.

## Credits

- [Ash Allen](https://ashallendesign.co.uk)
- [All Contributors](https://github.com/ash-jc-allen/type-safe/graphs/contributors)

## Changelog

Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
