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

