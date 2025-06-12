# HTML5 Pattern Generator

This library provides utilities to build HTML5 pattern attributes for client-side form validation.

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require somework/html5-pattern-generator
```

Alternatively, clone the repository and install dependencies:

```bash
git clone <repository-url>
cd html5-pattern-generator
composer install
```

## Usage

Classes are autoloaded using PSR-4 under the `Html5PatternGenerator` namespace. Basic usage example:

```php
use Html5PatternGenerator\PatternGenerator;

$generator = new PatternGenerator();
$pattern = $generator->generate();
```

### Date pattern

Generate a regex for `YYYY-MM-DD` formatted dates:

```php
use Html5PatternGenerator\Pattern\DatePatternGenerator;

$datePattern = DatePatternGenerator::pattern();
```

The pattern ensures only valid calendar dates are matched, so combinations like
`2024-02-31` are rejected.

Use a different format:

```php
$euPattern = DatePatternGenerator::pattern('d.m.Y');
```

Generate a pattern for a range of dates (inclusive):

```php
$from = new DateTimeImmutable('2024-05-01');
$to = new DateTimeImmutable('2024-05-10');
$rangePattern = DatePatternGenerator::between($from, $to);
```

For convenience you can generate patterns for a limited number of days after or before a date:

```php
$futurePattern = DatePatternGenerator::after(new DateTimeImmutable('2024-06-01'), 7);
$pastPattern = DatePatternGenerator::before(new DateTimeImmutable('2024-06-01'), 7);
```

