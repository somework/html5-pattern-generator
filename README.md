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
`2024-02-31` are rejected. Leap years are **not** validated, meaning
`2023-02-29` will still pass the regex. If you need to ensure a date exists,
validate it with PHP's `checkdate()` or attempt to create a
`DateTimeImmutable` instance after matching the pattern.

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

### Week pattern

Generate a regex for `YYYY-Www` formatted ISO weeks:

```php
use Html5PatternGenerator\Pattern\WeekPatternGenerator;

$weekPattern = WeekPatternGenerator::pattern();
```

### Color pattern

Generate a regex for HTML hexadecimal colors:

```php
use Html5PatternGenerator\Pattern\ColorPatternGenerator;

$colorPattern = ColorPatternGenerator::pattern();
```


### Configurable patterns

`PatternGenerator` can build simple character class based expressions. Options
include enabling alpha characters, digits and additional allowed characters as
well as length limits.

```php
$generator = new PatternGenerator([
    'alpha' => true,
    'digits' => false,
    'additional' => '-_',
    'min' => 2,
    'max' => 4,
]);
$pattern = $generator->generate();
// [A-Za-z\-_]{2,4}
```

Generate a pattern for a numeric range using the helper method:

```php
$monthPattern = PatternGenerator::numericRange(1, 12);
```

### Troubleshooting

If patterns do not behave as expected:

* Ensure special characters are escaped using `additional` or `numericRange`.
* When running browser tests on different Node versions, install Playwright
  browsers after switching Node to avoid mismatch errors.


## Running browser tests locally

Browser tests use [Playwright](https://playwright.dev/). Install PHP and Node dependencies, then install the Playwright browsers:

```bash
composer install
npm install
npx playwright install --with-deps
npm run test:browser
```

## Development

Use Composer scripts to run checks during development:

```bash
composer test  # run unit tests
composer cs    # check coding style
composer stan  # run static analysis
```
